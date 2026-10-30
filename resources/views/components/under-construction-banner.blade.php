<div id="appUnderConstructionBanner" class="app-construction-banner" role="alert">
    <div class="banner-inner">
        <div class="banner-badge">
            <span class="banner-pulse-dot" aria-hidden="true"></span>
            <span>Coming Soon</span>
        </div>
        <p class="banner-text">
            This app is currently under construction and we'll release it officially soon.
        </p>
    </div>
    <button type="button" class="banner-close" aria-label="Close announcement" onclick="dismissAppBanner()">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
</div>

<style>
    .app-construction-banner {
        position: relative;
        z-index: 1030;
        width: 100%;
        background: linear-gradient(135deg, #09132b 0%, #132758 45%, #0d1b3e 100%);
        border-bottom: 1px solid rgba(59, 130, 246, 0.28);
        box-shadow: 0 4px 18px rgba(9, 19, 43, 0.18);
        padding: 9px 42px 9px 16px;
        color: #f1f5f9;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .app-construction-banner .banner-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 8px 14px;
        max-width: 1200px;
        margin: 0 auto;
        text-align: center;
    }

    .app-construction-banner .banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.35);
        border-radius: 999px;
        color: #fbbf24;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        line-height: 1;
        white-space: nowrap;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.15);
    }

    .app-construction-banner .banner-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #f59e0b;
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        animation: bannerDotPulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }

    @keyframes bannerDotPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            transform: scale(0.95);
        }
        70% {
            box-shadow: 0 0 0 6px rgba(245, 158, 11, 0);
            transform: scale(1);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            transform: scale(0.95);
        }
    }

    .app-construction-banner .banner-text {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.45;
        color: #e2e8f0;
        letter-spacing: 0.1px;
    }

    .app-construction-banner .banner-close {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border: 0;
        background: rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        font-size: 11px;
    }

    .app-construction-banner .banner-close:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }

    @media (max-width: 575.98px) {
        .app-construction-banner {
            padding: 8px 36px 8px 12px;
        }

        .app-construction-banner .banner-inner {
            gap: 6px;
        }

        .app-construction-banner .banner-text {
            font-size: 12px;
        }

        .app-construction-banner .banner-badge {
            font-size: 10px;
            padding: 2px 8px;
        }
    }
</style>

<script>
    function dismissAppBanner() {
        var banner = document.getElementById('appUnderConstructionBanner');
        if (banner) {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-8px)';
            setTimeout(function() {
                if (banner && banner.parentNode) {
                    banner.parentNode.removeChild(banner);
                }
            }, 250);
        }
    }
</script>
