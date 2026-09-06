@extends('layouts.app')

@section('title', 'Audit Logs')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        .audit-log-page {
            padding: 24px;
        }
        .audit-log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .audit-log-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }
        .audit-log-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 14px;
        }
        .audit-log-table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        .audit-log-table-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .audit-log-table-card-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #0f172a;
        }
        .audit-log-table-card-header p {
            margin: 2px 0 0;
            color: #64748b;
            font-size: 13px;
        }
        .table-diff-value {
            background: #f8fafc;
            border-radius: 6px;
            padding: 8px 12px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
        .diff-added {
            background-color: #dcfce7;
            color: #166534;
        }
        .diff-removed {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
@endpush

@section('content')
<div class="audit-log-page">

    {{-- Page Header --}}
    <div class="audit-log-header">
        <div>
            <h1>Audit Logs</h1>
            <p>Append-only system activity and change history logs</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="audit-log-table-card">
        <div class="audit-log-table-card-header">
            <div>
                <h2>Activity Log</h2>
                <p><span id="audit-log-record-count">0</span> audit entries found</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="audit-logs-table"
                   class="table table-hover table-bordered nowrap"
                   style="width:100%">
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Module</th>
                        <th>IP Address</th>
                        <th style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

{{-- Detail Modal --}}
<div class="modal fade" id="audit-log-detail-modal" tabindex="-1" role="dialog" aria-labelledby="audit-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="modal-title" id="audit-modal-title" style="font-weight: 700; color: #1e293b;">Audit Log Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>User:</strong> <span id="modal-audit-user"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Event:</strong> <span id="modal-audit-event"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Module:</strong> <span id="modal-audit-module"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Record:</strong> <span id="modal-audit-record"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date &amp; Time:</strong> <span id="modal-audit-date-time"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>IP Address:</strong> <span id="modal-audit-ip"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>User Agent:</strong> <span id="modal-audit-ua" class="small text-muted" style="word-break: break-all;"></span>
                    </div>
                </div>

                <hr>

                <h6 class="font-weight-bold mb-3">Changes Diff</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Field</th>
                                <th class="w-50">Previous Value (Old)</th>
                                <th class="w-50">New Value</th>
                            </tr>
                        </thead>
                        <tbody id="modal-audit-diff-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script>
    (function ($) {
        'use strict';

        var auditLogsIndexUrl = @json(route('audit-logs.index'));
        var csrfToken         = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

        var auditTable = $('#audit-logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: auditLogsIndexUrl,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            columns: [
                { data: 'created_at_formatted',  name: 'created_at_formatted' },
                { data: 'user_name',             name: 'user_name' },
                { data: 'event_badge',          name: 'event_badge' },
                { data: 'module_name',           name: 'module_name' },
                { data: 'ip_address_display',    name: 'ip_address_display' },
                { data: 'action',                name: 'action', searchable: false, orderable: false }
            ],
            language: { search: '', searchPlaceholder: 'Search audit logs...' }
        });

        auditTable.on('draw', function () {
            var info = auditTable.page.info();
            $('#audit-log-record-count').text(info.recordsTotal || 0);
        });

        $(document).on('click', '.js-audit-log-view', function () {
            var id = $(this).data('id');
            $.ajax({
                url: auditLogsIndexUrl + '/' + id,
                method: 'GET',
                success: function (data) {
                    $('#modal-audit-user').text(data.user);
                    $('#modal-audit-event').html(data.event_badge);
                    $('#modal-audit-module').text(data.module);
                    $('#modal-audit-record').text('#' + data.record_id);
                    $('#modal-audit-date-time').text(data.date_time);
                    $('#modal-audit-ip').text(data.ip_address);
                    $('#modal-audit-ua').text(data.user_agent);

                    var $tbody = $('#modal-audit-diff-body');
                    $tbody.empty();

                    var oldVal = data.old_values || {};
                    var newVal = data.new_values || {};
                    var allKeys = Array.from(new Set([...Object.keys(oldVal), ...Object.keys(newVal)]));

                    if (allKeys.length === 0) {
                        $tbody.append('<tr><td colspan="3" class="text-center text-muted">No attribute details recorded</td></tr>');
                    } else {
                        allKeys.forEach(function (key) {
                            var oldStr = oldVal.hasOwnProperty(key) ? (typeof oldVal[key] === 'object' ? JSON.stringify(oldVal[key], null, 2) : oldVal[key]) : '<em>(none)</em>';
                            var newStr = newVal.hasOwnProperty(key) ? (typeof newVal[key] === 'object' ? JSON.stringify(newVal[key], null, 2) : newVal[key]) : '<em>(none)</em>';

                            $tbody.append(
                                '<tr>' +
                                    '<td><strong>' + key + '</strong></td>' +
                                    '<td class="table-diff-value diff-removed">' + oldStr + '</td>' +
                                    '<td class="table-diff-value diff-added">' + newStr + '</td>' +
                                '</tr>'
                            );
                        });
                    }

                    $('#audit-log-detail-modal').modal('show');
                }
            });
        });

    })(jQuery);
    </script>
@endpush
