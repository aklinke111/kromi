<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";
//include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/css/styles.css";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "nestedListSortly"){
        $sql = "SELECT sid as id, name, sortlyId, pid as parent_id FROM sortly order by name";
        $result = $db->query($sql);

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
       $nestedCategories = buildTree($categories); 
    }
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



function buildList($items) {
    $html = '<ul>';
    foreach ($items as $item) {
        $hasChildren = isset($item['children']) && !empty($item['children']);
        $html .= '<li' . (!$hasChildren ? ' class="final-level"' : '') . '>';
        
        if (!$hasChildren && isset($item['sortlyId'])) {
            $html .= '<span class="stop-icon">;</span>'; // Stop icon symbol
            $html .= ' <span class="additional-info">' . htmlspecialchars($item['name']). '  ['. htmlspecialchars($item['sortlyId']) . '] </span>';
        } else {
            $html .= '<span class="expandable' . ($hasChildren ? ' has-children' : '') . '">' . htmlspecialchars($item['name']) . '</span>';
        }
        
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
    <title>KROMI structure from SORTLY MySQL</title>
    <h3>KROMI structure from SORTLY MySQL</h3>
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
            color: green;
            margin-right: 5px;
        }
        .stop-icon {
            content: '\9642';
            color: gray; /* Gray color for stop icon */
            margin-right: 5px;
            font-size: 10px; /* Adjust font size as needed */
        }
        .hidden {
            display: none;
        }
        .has-children.expanded::before {
            content: '\25BC'; /* Down-pointing triangle */
        }
        .final-level .expandable {
            color: green; /* Green color for final-level items */
        }
        .additional-info {
            color: gray; /* Gray color for additional info */
            font-style: normal; /* Italic font style for additional info */
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
