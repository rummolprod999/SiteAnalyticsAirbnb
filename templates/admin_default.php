<?php require_once 'templates/admin_navigation.php'; ?>
<div id="article">
    <div><h1 class="text-center">USERS LIST</h1></div>
    <?php if (isset($_SESSION['add_user'])) {
        echo "<div class='alert alert-success' role='alert'>{$_SESSION['add_user']}</div>";
        unset($_SESSION['add_user']);
    }
    if (isset($_SESSION['remove_user'])) {
        echo "<div class='alert alert-danger' role='alert'>{$_SESSION['remove_user']}</div>";
        unset($_SESSION['remove_user']);
    } ?>
    <div id="table_div">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User name</th>
                    <th>Proxy address</th>
                    <th>Proxy Port</th>
                    <th>Proxy User</th>
                    <th>Proxy Pass</th>
                    <th>Last Activity</th>
                    <th>Change User</th>
                    <th>Remove User</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['users_list'] as $row): ?>
                    <tr>
                        <td><strong><?php echo $row['id'] ?></strong></td>
                        <td><strong><?php echo $row['user_name'] ?></strong></td>
                        <td><strong><?php echo $row['proxy_address'] ?></strong></td>
                        <td><strong><?php echo $row['proxy_port'] ?></strong></td>
                        <td><strong><?php echo $row['proxy_user'] ?></strong></td>
                        <td><strong><?php echo $row['proxy_pass'] ?></strong></td>
                        <td><strong><?php echo $row['last_date'] ?></strong></td>
                        <td>
                            <form class="form-inline" method="get" action="/change_user"><input type="hidden"
                                                                                                name="user_id"
                                                                                                value='<?php echo $row['id'] ?>'>
                                <button type="submit" class="btn btn-warning mb-2">Change</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($row['is_admin'] !== '1'): ?>
                                <form class="form-inline" method="post"><input type="hidden" name="remove_user"
                                                                               value='<?php echo $row['id'] ?>'>
                                    <button type="submit" class="btn btn-danger mb-2">Remove</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 border border-2 border-secondary rounded mb-3 p-2">
                <div><p><strong>Add new user</strong></p></div>
                <form class="form" method="post"
                      oninput='pass.setCustomValidity(pass.value != confirm_pass.value ? "Passwords do not match." : "")'>
                    <div class="form-group">
                        <label for="name_user">Name</label>
                        <input type="text" class="form-control" id="name_user"
                               placeholder=""
                               value="" name="name_user" required>
                    </div>
                    <div class="form-group">
                        <label for="pass">Password</label>
                        <input type="password" class="form-control" id="pass"
                               placeholder=""
                               value="" name="pass" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPass">Confirm password</label>
                        <input type="password" class="form-control" id="confirmPass"
                               value="" name="confirm_pass" required>
                    </div>
                    <div class="form-group">
                        <label for="proxyAddr">Proxy address</label>
                        <input type="text" class="form-control" id="proxyAddr"
                               placeholder=""
                               pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                               value="" name="proxy_address" required>
                    </div>
                    <div class="form-group">
                        <label for="proxyPort">Proxy port</label>
                        <input type="text" class="form-control" id="proxyPort"
                               placeholder="" pattern="\d{1,5}"
                               value="" name="proxy_port" required>
                    </div>
                    <div class="form-group">
                        <label for="proxyUser">Proxy user</label>
                        <input type="text" class="form-control" id="proxyUser"
                               placeholder="" pattern=".+"
                               value="" name="proxy_user" required>
                    </div>
                    <div class="form-group">
                        <label for="proxyPass">Proxy password</label>
                        <input type="text" class="form-control" id="proxyPass"
                               placeholder="" pattern=".+"
                               value="" name="proxy_pass" required>
                    </div>
                    <button type="submit" class="btn btn-success mb-2">Add new user</button>
                </form>
            </div>
        </div>
    </div>
</div>

