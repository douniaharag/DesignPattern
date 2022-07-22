<h1>Tags</h1>

<?php

if ((isset($_POST['action']) && $_POST['action'] !== null)) :
    switch ($_POST['action']):
        case 'create':
            $view->create = 'create';
            $view->includePartial("form", $tag->getFormTags($tag));
            break;
        case 'delete':
        case 'update':
            break;
    endswitch;
elseif (isset($action) && $action !== null) :
    switch ($action):
        case 'update':
            $view->update = 'update';
            $view->includePartial("form", $category->getFormUpdateTags($postById));
            break;
        case 'delete':
            break;
    endswitch;
else :
    $view->includePartial("posts", $tags);
endif;

?>