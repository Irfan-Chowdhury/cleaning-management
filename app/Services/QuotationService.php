<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationService
{
    /**
     * Persist a new quotation and its line items inside a single DB transaction.
     *
     * @param  array  $data  Validated data from StoreQuotationRequest
     */
    public function store(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $totals = $this->calculateTotals(array_column($data['items'], 'amount'));

            $quotation = Quotation::create([
                'quote_number'   => $this->generateSequentialQuoteNumber(),
                'quote_date'     => $data['quote_date'],
                'abn'            => $data['abn'] ?? null,
                'contact_person' => $data['contact_person'],
                'company_name'   => $data['company_name'] ?? null,
                'address'        => $data['address'],
                'phone'          => $data['phone'],
                'email'          => $data['email'],
                'frequency'      => $data['frequency'],
                'shift_schedule' => $data['shift_schedule'],
                'subtotal'       => $totals['subtotal'],
                'gst_amount'     => $totals['gst_amount'],
                'total_amount'   => $totals['total_amount'],
                'terms_notes'    => $data['terms_notes'] ?? null,
                'status'         => 'draft',
            ]);

            $this->syncItems($quotation, $data['items']);

            return $quotation->load('items');
        });
    }

    /**
     * Update an existing quotation and replace its line items inside a transaction.
     *
     * @param  array  $data  Validated data from UpdateQuotationRequest
     */
    public function update(Quotation $quotation, array $data, bool $convertToInvoice = false): Quotation
    {
        return DB::transaction(function () use ($quotation, $data, $convertToInvoice) {
            $quotation = Quotation::whereKey($quotation->id)->lockForUpdate()->firstOrFail();

            if ($convertToInvoice && $quotation->status !== 'draft') {
                throw new \DomainException('Only draft quotations can be invoiced.');
            }

            $totals = $this->calculateTotals(array_column($data['items'], 'amount'));

            $quotation->update([
                'quote_date'     => $data['quote_date'],
                'abn'            => $data['abn'] ?? null,
                'contact_person' => $data['contact_person'],
                'company_name'   => $data['company_name'] ?? null,
                'address'        => $data['address'],
                'phone'          => $data['phone'],
                'email'          => $data['email'],
                'frequency'      => $data['frequency'],
                'shift_schedule' => $data['shift_schedule'],
                'subtotal'       => $totals['subtotal'],
                'gst_amount'     => $totals['gst_amount'],
                'total_amount'   => $totals['total_amount'],
                'terms_notes'    => $data['terms_notes'] ?? $quotation->terms_notes,
                'status'         => $convertToInvoice ? 'invoiced' : $quotation->status,
            ]);

            $this->syncItems($quotation, $data['items']);

            return $quotation->fresh('items');
        });
    }

    /**
     * Permanently delete a quotation and cascade-delete its items.
     */
    public function delete(Quotation $quotation): void
    {
        DB::transaction(function () use ($quotation) {
            $quotation->items()->delete();
            $quotation->delete();
        });
    }

    /**
     * Replace all line items for a quotation.
     * Deletes existing items then re-inserts in bulk for atomic replacement.
     *
     * @param  array  $items  Array of ['description' => string, 'amount' => numeric]
     */
    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();

        $rows = [];
        foreach ($items as $index => $item) {
            $rows[] = [
                'quotation_id' => $quotation->id,
                'description'  => $item['description'],
                'amount'       => (float) $item['amount'],
                'sort_order'   => $index,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        QuotationItem::insert($rows);
    }

    /**
     * Generate the next sequential quote number based on the last row ID in the database.
     * Format: D2G-{0001} up to D2G-{9999}, dynamically expanding to 5+ digits (e.g. D2G-10000).
     */
    public function generateSequentialQuoteNumber(): string
    {
        $prefix = 'D2G-';

        // Determine the next sequence number based on the last row ID in database
        $lastId = (int) (Quotation::max('id') ?? 0);

        // Also check numeric suffix of the last recorded quote number to maintain monotonicity
        $lastQuote = Quotation::orderBy('id', 'desc')->value('quote_number');
        $lastQuoteNum = 0;
        if ($lastQuote && preg_match('/(\d+)$/', $lastQuote, $m)) {
            $lastQuoteNum = (int) $m[1];
        }

        $next = max($lastId, $lastQuoteNum) + 1;
        $padLength = max(4, strlen((string) $next));

        return $prefix . str_pad((string) $next, $padLength, '0', STR_PAD_LEFT);
    }


    public function calculateTotals(array $amounts): array
    {
        $subtotal   = round(array_sum($amounts), 2);
        $gstAmount  = round($subtotal * 0.10, 2);
        $total      = round($subtotal + $gstAmount, 2);

        return [
            'subtotal'     => $subtotal,
            'gst_amount'   => $gstAmount,
            'total_amount' => $total,
        ];
    }
}
