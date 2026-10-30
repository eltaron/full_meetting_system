<link rel="stylesheet" href="{{ asset('css/kfs-royal.css') }}">

<style>
    :root {
        --kfs-panel-green: #1F6E51;
        --kfs-panel-green-dark: #144536;
        --kfs-panel-gold: #C9A227;
    }

    /* لمسات الهوية الموحّدة داخل لوحات Filament */
    .fi-sidebar {
        background-image:
            linear-gradient(135deg, rgba(255, 255, 255, .035) 25%, transparent 25%, transparent 75%, rgba(255, 255, 255, .035) 75%),
            linear-gradient(45deg, rgba(255, 255, 255, .035) 25%, transparent 25%, transparent 75%, rgba(255, 255, 255, .035) 75%);
        background-size: 42px 42px;
        background-position: 0 0, 21px 21px;
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(201, 162, 39, .28);
    }

    .fi-logo {
        font-weight: 900;
        letter-spacing: -.2px;
    }

    .fi-topbar {
        border-bottom: 1px solid #E4E0D4;
    }

    .fi-ta-record:hover {
        background-color: #F6F7F2;
    }
</style>
