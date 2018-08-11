# IM
WordPress Theme Study
BUG：
1.帖子评论区：如果显示评论为倒序，添加新评论后如显示有待审核的评论，楼层计数错误！@mo_comments_list.php

0809
mo_minicat.php、mo_notice.php、mo_posts_related.php
Replace function query_posts() due to some reasons.

0811
主题设置中的文本框提交，会过滤部分HTML以及SCRIPT标签，故对textarea更改其净化功能of_sanitize_textarea。