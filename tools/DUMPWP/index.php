<?php
define('WP_INSTALLING', false);

include '../../wp-config.php';
ECHO ABSPATH;

$sql = '';
$dir = opendir(dirname(__FILE__));
while ($file = readdir($dir)) {
    if (substr($file, -4, 4) == '.sql')
        $sql .= '<option value="' . $file . '">' . $file . '</option>';
}

echo $sql;
if (!$_POST) {
    $_POST['user'] = DB_USER;
    $_POST['password'] = DB_PASSWORD;
    $_POST['db'] = DB_NAME;
    $_POST['hostold'] = get_bloginfo('siteurl');
}
$_POST['hostnew'] = (!$_POST['hostnew']) ? 'http://' . $_SERVER['SERVER_NAME'] . '/' : $_POST['hostnew'];
?>
<style>
    input {
        width : 300px;
        border : 1px solid #ccc;
    }
    label {
        display : block;
        float : left;
        width : 300px;
    }
</style>
<form id="forms" method="post">
    <fieldset>
        <label>HostDB</label><input type="text" name="host" value="localhost"/><br/>
        <label>user</label><input type="text" name="user" value="<?php echo $_POST['user']; ?>"/><br/>
        <label>password</label><input type="text" name="password" value="<?php echo $_POST['password']; ?>"/><br/>
        <label>DBname</label><input type="text" name="db" value="<?php echo $_POST['db']; ?>"/><br/>

        <?php if (!empty($sql)) { ?>
            <label>sqlFile</label><select name="sqlfile">
                <?php echo $sql; ?>
            </select>
            <br/><br/>
            <input type="submit" name="importsql" value="SQL import"/>
        <?php } else { ?>
            no sql file.
        <?php } ?>
    </fieldset>

    <fieldset>
        <label>hostold (http://***.feed.colorz.fr/)</label><input type="text" name="hostold" value="<?php echo $_POST['hostold']; ?>"/><br/>
        <label>hostnew (http://domain.ltd/)</label><input type="text" name="hostnew" value="<?php echo $_POST['hostnew']; ?>"/><br/>

        <input type="submit"  name="updatedb" value="reset db host"/>
    </fieldset>
</form>

<?php
if ($_POST['importsql']) {
    system("cat " . $_POST['sqlfile'] . " | mysql --host=" . $_POST['host'] . " --user=" . $_POST['user'] . " --password=" . $_POST['password'] . " --default-character-set=utf8 " . $_POST['db'] . ""); // --defaults-character-set=utf8 --default-character-set=utf8
    echo 'Import SQL : Done';
}

if ($_POST['updatedb']) {
    if (!$_POST['hostnew'])
        die('host error');
    if (!$_POST['hostold'])
        die('host error');
    global $wpdb;

    $wpdb->query('update ' . $wpdb->posts . ' set guid = replace(guid,"' . $_POST['hostold'] . '","' . $_POST['hostnew'] . '")');
    $wpdb->query('update ' . $wpdb->posts . ' set post_content = replace(post_content, "' . $_POST['hostold'] . '","' . $_POST['hostnew'] . '")');
    $wpdb->query('update ' . $wpdb->postmeta . 'a set meta_value = replace(meta_value, "' . $_POST['hostold'] . '","' . $_POST['hostnew'] . '")');

    $wpdb->query('update ' . $wpdb->options . ' SET option_value="' . $_POST['hostnew'] . '" WHERE option_name="home"');
    $wpdb->query('update ' . $wpdb->options . ' SET option_value="' . $_POST['hostnew'] . '" WHERE option_name="siteurl"');

    /*
      update wp_posts set guid = replace(guid,'http://selectiveclub.feed.colorz.fr/','http://www.selective-club.com/');
      update wp_posts set post_content = replace(post_content, 'http://selectiveclub.feed.colorz.fr/','http://www.selective-club.com/');


      update wp_options SET option_value="http://www.selective-club.com/" WHERE option_name="home";
      update wp_options SET option_value="http://www.selective-club.com/" WHERE option_name="siteurl";

     */
}
