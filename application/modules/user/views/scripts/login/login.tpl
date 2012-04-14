<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
    <form enctype="application/x-www-form-urlencoded" method="post" action="">
        <dl class="zend_form">
            <dt id="username-label"><label for="username" class="required">用户名:</label></dt>
            <dd id="username-element">
                <input type="text" name="username" id="username" value=""  style="width:120px;" /></dd>
            <dt id="password-label"><label for="password" class="required">密码:</label></dt>
            <dd id="password-element">
                <input type="password" name="password" id="password" value="" style="width:120px;" /></dd>
            <dt id="loginsubmit-label">&#160;</dt><dd id="loginsubmit-element">
                <input type="submit" name="loginsubmit" id="loginsubmit" value="登录" /></dd>
        </dl>
    </form>
</div>