<?php
header("X-author: nla")
?>

<h2>Cùng 1 form, khác method</h2>

<div style="display:flex; gap:40px">
    <div>
        <h3>Form POST</h3>
        <form action="result.php" method="POST">
            <div>Username: <input type="text" name="username"></div>
            <div>Password: <input type="password" name="password"></div>
            <input type="submit" value="Gửi bằng POST">
        </form>
    </div>
</div>

<p><a href="controller/news.php?title=Demo">→ Về demo router</a></p>