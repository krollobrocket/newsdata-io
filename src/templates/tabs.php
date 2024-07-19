<?php
    $currentTab = filter_input(INPUT_GET, 'tab', FILTER_UNSAFE_RAW) ?? 'latest';
?>
<h2 class="nav-tab-wrapper">
    <a class="nav-tab <?php echo $currentTab === 'latest' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=newsdata-io&tab=latest')); ?>">Latest</a>
    <a class="nav-tab <?php echo $currentTab === 'crypto' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=newsdata-io&tab=crypto')); ?>">Crypto</a>
    <a class="nav-tab <?php echo $currentTab === 'archive' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=newsdata-io&tab=archive')); ?>">Archive</a>
</h2>
