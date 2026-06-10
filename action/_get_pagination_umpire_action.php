<?php
include('action_object.php');
// --- Site settings ---
$site_title_res = mysqli_fetch_assoc($data->web_setting_details('web_settings', '1'));
$site_url_res   = mysqli_fetch_assoc($data->web_setting_details('web_settings', '197'));
$site_url = $site_url_res['field_value'];

// --- Pagination setup ---
$baseURL = '_get_pagination_umpire_action.php';
$offset  = !empty($_POST['page']) ? (int)$_POST['page'] : 0;
$limit   = 20;

$filters = [
    'search_value_1' => $_POST['search_value_1'] ?? '',
    'search_value_2' => $_POST['search_value_2'] ?? '',
    'search_value_3' => $_POST['search_value_3'] ?? '',
    'search_value_4' => $_POST['search_value_4'] ?? '',
    'search_value_5' => $_POST['search_value_5'] ?? '',
    'search_value_6' => $_POST['search_value_6'] ?? '',
    'search_value_7' => $_POST['search_value_7'] ?? '',
    'search_value_8' => $_POST['search_value_8'] ?? '',
    'search_value_9' => $_POST['search_value_9'] ?? ''
];

// --- Count total filtered records ---
// --- Fetch filtered umpire records AND total count in 1 query ---
$result = $data->get_filtered_umpires_with_count($filters, $offset, $limit);

// Total number of filtered rows (for pagination)
$rowCount = $result['total'];

// Paginated data
$umpire_record_row_fetch = $result['data'];
$umpire_record_rowcount = count($umpire_record_row_fetch);

// --- Pagination config ---
$pagConfig = [
    'baseURL'     => $baseURL,
    'totalRows'   => $rowCount,
    'perPage'     => $limit,
    'currentPage' => $offset,
    'contentDiv'  => 'dataContainer'
];
$pagination = new Pagination($pagConfig);
?>

<!-- ==================== TABLE ==================== -->
<div class="result_filter">
    <div class="left_rusult_pre">
        <p><?php echo $rowCount; ?> Results</p>
    </div>
    <div class="right_reset_but_umpire right_reset_but">
        <a href="javascript:void(0);">Reset all filters<span class="glyphicon glyphicon-remove"></span></a>
    </div>
</div>
<table id="umpires_table">
<thead>
<tr>
<th>Name</th>
<th>Number of Matches</th>
<th>Discipline</th>
<th>First Match Date</th>
<th>Last Match Date</th>
<th>Competition</th>
<th></th>
</tr>
</thead>
<tbody>
<?php
if ($umpire_record_rowcount > 0) {
    foreach ($umpire_record_row_fetch as $umpire_row) {
        $umpire_id   = $umpire_row['umpire_id'];
        $umpire_name = $umpire_row['umpire_name'];
        $umpire_type = $umpire_row['umpire_type'];

        // Competitions
        // $competitions = $data->aflua_all_umpire_competetions('matches_record', $umpire_id, 1, 1);
        // $competitions_html = '';
        // if (!empty($competitions)) {
        //     foreach ($competitions as $comp_name) {
        //         $slug = strtolower(trim($comp_name));
        //         $competitions_html .= "<span class='comp_col {$slug}'>".strtoupper($comp_name)."</span>";
        //     }
        // } else {
        //     $competitions_html = '<span class="comp_col badge-none">No competitions</span>';
        // }

        //Process umpire types

        $umpire_type_html = '';
        if (!empty($umpire_type)) {
            $ump_type = explode(',', $umpire_type);
            foreach ($ump_type as $type_name) {
                $type_name_class = strtolower(trim($type_name));
                $umpire_type_html .= "<span class=\"comp_col {$type_name_class}\">" . strtoupper($type_name) . "</span>";
            }
        } else {
            $umpire_type_html = '<span class="comp_col badge-none">No record</span>';
        }

        $competitions_html = '';
        if (!empty($umpire_row['competitions'])) {
            $competitions = explode(',', $umpire_row['competitions']);
            foreach ($competitions as $comp_name) {
                $comp_name = trim($comp_name);
                $slug = strtolower($comp_name);
                $competitions_html .= "<span class='comp_col {$slug}'>".strtoupper($comp_name)."</span>";
            }
        } else {
            $competitions_html = '<span class="comp_col badge-none">No competitions</span>';
        }

       $search_value_3 = $_POST['search_value_3'];
        if ($search_value_3) {
            $get_disc_no = $data->get_filtered_umpires_with_count_by_discipline('matches_record', $search_value_3, $umpire_id);
            $discipline_count = mysqli_num_rows($get_disc_no);
        } else {
            $discipline_count = $umpire_row['match_count'];
        }

        // {$umpire_row['match_count']}/

        

        echo "<tr>
            <td class='mn'>".ucwords($umpire_name)."</td>
            <td class='mn'>".$discipline_count."</td>
            <td>{$umpire_type_html}</td>
            <td>".(!empty($umpire_row['earliest_date']) ? date('d/m/Y', strtotime($umpire_row['earliest_date'])) : 'N/A')."</td>
            <td>".(!empty($umpire_row['latest_date']) ? date('d/m/Y', strtotime($umpire_row['latest_date'])) : 'N/A')."</td>
            <td>{$competitions_html}</td>
            <td><a href='javascript:void(0);' profile_id='{$umpire_id}' class='view-link view-link-umpire'>View profile</a></td>
        </tr>";
    }
} 
?>
</tbody>
</table>

<!-- ==================== PAGINATION ==================== -->

<?php
$totalPages = ceil($rowCount / $limit);
if ($totalPages > 1) {
    echo '<div class="pagination">';
    
    // Previous
    $prev = max(0, $offset - $limit);
    if ($offset > 0) {
        echo "<button><a href='#' data-page='{$prev}' class='prev'><img src='{$site_url}/assets/images/left-arrow.png' alt='Previous'>Previous</a></button>";
    } else {
        echo "<button class='prev' disabled><img src='{$site_url}/assets/images/left-arrow.png' alt='Previous'>Previous</button>";
    }

    // Page numbers
    // echo '<div class="pages pagination_desktop">';
    // for ($i = 0; $i < $totalPages; $i++) {
    //     $pageNum = $i + 1;
    //     $pageOffset = $i * $limit;
    //     if ($pageOffset == $offset) {
    //         echo "<span class='active'>{$pageNum}</span>";
    //     } else {
    //         echo "<span><a href='#' data-page='{$pageOffset}'>{$pageNum}</a></span>";
    //     }
    // }
    // echo '</div>';

    //dots
    echo '<div class="pagination_mobile">';
    $currentPage = floor($offset / $limit) + 1;
    $range = 1; // pages before & after current
    $firstPages = 2;
    $lastPages = 2;

    $ellipsisShownLeft = false;
    $ellipsisShownRight = false;

    for ($i = 1; $i <= $totalPages; $i++) {

        $pageOffset = ($i - 1) * $limit;

        $show =
            $i <= $firstPages ||                             // first pages
            $i > $totalPages - $lastPages ||                 // last pages
            abs($i - $currentPage) <= $range;                // near current

        if ($show) {
            if ($pageOffset == $offset) {
                echo "<span class='active'>{$i}</span>";
            } else {
                echo "<span><a href='#' data-page='{$pageOffset}'>{$i}</a></span>";
            }
        } else {
            if ($i < $currentPage && !$ellipsisShownLeft) {
                echo "<span class='dots'>…</span>";
                $ellipsisShownLeft = true;
            }
            if ($i > $currentPage && !$ellipsisShownRight) {
                echo "<span class='dots'>…</span>";
                $ellipsisShownRight = true;
            }
        }
    }

    echo '</div>';

    // Next
    $next = min(($totalPages - 1) * $limit, $offset + $limit);
    if ($offset + $limit < $rowCount) {
        echo "<button><a href='#' data-page='{$next}' class='next'>Next<img src='{$site_url}/assets/images/left-arrow.png' alt='next'></a></button>";
    } else {
        echo "<button class='next' disabled>Next<img src='{$site_url}/assets/images/left-arrow.png' alt='next'></button>";
    }

    echo '</div>';
}
?>
