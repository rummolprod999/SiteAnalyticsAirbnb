<div id="header">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php active(''); ?>" href="/">Main</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php active('settings'); ?>" href="/settings">Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php active('analytics'); ?>" href="/analytics">Analytics</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php active('analytics2'); ?>" href="/analytics2">Analytics2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger <?php active('/?action=out'); ?>"
               href="/?action=out">Logout <?php if (isset($_SESSION['user_name'])) {
                    echo " {$_SESSION['user_name']}";
                } ?></a>
        </li>
    </ul>


</div>