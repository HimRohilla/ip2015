<h3>Login</h3>
<p><?php echo SESSION::flash("error"); ?></p>
<form action="<?php echo ROUTE::getAbsoluteURL("User Menu"); ?>" method="POST">
    Username : <input type="text" name="username"><br><br>
    Password : <input type="text" name="password"><br><br>
    <input type="hidden" name="csrf_token" value="<?php echo INPUT::getToken(); ?>">
    <input type="submit"  name="submit">
</form>