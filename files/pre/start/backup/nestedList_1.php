<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";


$sql = "SELECT sid as id, name, sortlyId, pid as parent_id FROM sortly";
$result = $db->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

function buildTree(array $elements, $parentId = 0) {
    $branch = [];

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }

    return $branch;
}

$nestedCategories = buildTree($categories);

function buildList($items) {
    $html = '<ul>';
    foreach ($items as $item) {
        $hasChildren = isset($item['children']) && !empty($item['children']);
        $html .= '<li>';
        $html .= '<span class="expandable' . ($hasChildren ? ' has-children' : '') . '">' . htmlspecialchars($item['name']) .' - '.htmlspecialchars($item['sortlyId']). '</span>';
        if ($hasChildren) {
            $html .= '<ul class="hidden">';
            $html .= buildList($item['children']);
            $html .= '</ul>';
        }
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic Nested List from MySQL</title>
    <style>
        ul {
            list-style-type: none;
            padding-left: 20px;
        }
        .expandable {
            cursor: pointer;
            color: #2c3e50;
            padding: 5px;
            display: block;
        }
        .expandable:hover {
            background-color: #ecf0f1;
        }
        .has-children::before {
            content: '\25B6'; /* Right-pointing triangle */
            color: #2980b9;
            margin-right: 5px;
        }
        .hidden {
            display: none;
        }
        .has-children.expanded::before {
            content: '\25BC'; /* Down-pointing triangle */
        }
    </style>
</head>
<body>

<?php
echo buildList($nestedCategories);
?>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('.expandable.has-children').forEach(function (element) {
        element.addEventListener('click', function () {
            let nextUl = this.nextElementSibling;
            if (nextUl && nextUl.tagName.toLowerCase() === 'ul') {
                nextUl.classList.toggle('hidden');
                this.classList.toggle('expanded');
            }
        });
    });
});
</script>

</body>
</html>
