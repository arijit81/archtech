<?php 
include('action_object.php');

$site_title_result = $data->web_setting_details('web_settings','1');
$site_title_res = mysqli_fetch_assoc($site_title_result);

$site_url_result = $data->web_setting_details('web_settings','197');
$site_url_res = mysqli_fetch_assoc($site_url_result);

$site_description_result = $data->web_setting_details('web_settings','198');
$site_description_res = mysqli_fetch_assoc($site_description_result);

$site_url = $site_url_res['field_value'];


$filters = [];

$limit  = 9999;
$offset = 0;
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


$result = $data->get_filtered_umpires_with_count($filters, $offset, $limit);

// Total number of filtered rows (for pagination)
$rowCount = $result['total'];

// Paginated data
$umpire_record_row_fetch = $result['data'];
$umpire_record_rowcount = count($umpire_record_row_fetch);

if (!$result) {
    die('Database query failed: ' . mysqli_error($data->conn));
}

$time = time();

// set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Umpire-list' . ucwords($umpire_name) . '-'.$time.'.csv');

// open output stream
$output = fopen('php://output', 'w');


$custom_headers = ['Name', 'Number of Matches', 'Discipline', 'Fist Match Date', 'Last Match Date', 'Competition'];
fputcsv($output, $custom_headers);

 
if ($umpire_record_rowcount > 0) {
    foreach ($umpire_record_row_fetch as $umpire_row) {
        $umpire_id   = $umpire_row['umpire_id'];
        $umpire_name = $umpire_row['umpire_name'];
        $umpire_type = $umpire_row['umpire_type'];

        // Competitions
        $competitions_html = '';
        if (!empty($umpire_row['competitions'])) {
            $competitions = explode(',', $umpire_row['competitions']);
            foreach ($competitions as $comp_name) {
                $comp_name = trim($comp_name);
                $slug = strtolower($comp_name);
                $competitions_html .= strtoupper($comp_name);
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

        fputcsv($output, [
            ucwords($umpire_name),
            $discipline_count,
            $umpire_type,
            (!empty($umpire_row['earliest_date']) ? date('d/m/Y', strtotime($umpire_row['earliest_date'])) : 'N/A'),
            (!empty($umpire_row['latest_date']) ? date('d/m/Y', strtotime($umpire_row['latest_date'])) : 'N/A'),
            $competitions_html
        ]);
    }
}

fclose($output);
exit;
?>
