<?php
_moloader('mo_comments_list', false);
wp_list_comments('type=comment&callback=mo_comments_list');
paginate_comments_links('prev_next=0');