<?php
include('action_object.php');

$site_title_res = mysqli_fetch_assoc($data->web_setting_details('web_settings', '1'));
$site_url_res   = mysqli_fetch_assoc($data->web_setting_details('web_settings', '197'));
$site_url = $site_url_res['field_value'];

// Collect filter values
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

$year = $_POST['search_value_7'];

// --- Count total filtered records ---
$result = $data->get_filtered_umpires_with_count($filters, $offset, $limit);

// Total number of filtered rows (for pagination)
$rowCount = $result['total'];

// Paginated data
$umpire_record_row_fetch = $result['data'];
$umpire_record_rowcount = count($umpire_record_row_fetch);

// --- Pagination config ---

$html = '<h2 style="text-align:center;">Umpire List</h2>
        <div class="left_rusult_pre">
            <p>'.$rowCount.' Results</p>
        </div>
        <table id="umpires_table" border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Number of Matches</th>
                    <th>Discipline</th>
                    <th>First Match Date</th>
                    <th>Last Match Date</th>
                    <th>Competition</th>
                </tr>
            </thead>
            <tbody>';

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

        $html .= "<tr>
                    <td>".ucwords($umpire_name)."</td>
                    <td>".$discipline_count."</td>
                    <td>".ucwords($umpire_type)."</td>
                    <td>".(!empty($umpire_row['earliest_date']) ? date('d/m/Y', strtotime($umpire_row['earliest_date'])) : 'N/A')."</td>
                    <td>".(!empty($umpire_row['latest_date']) ? date('d/m/Y', strtotime($umpire_row['latest_date'])) : 'N/A')."</td>
                    <td>".$competitions_html."</td>
                </tr>";
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align:center;">No records found</td></tr>';
}

$html .= '</tbody></table>';

    // Generate PDF
    $mpdf = new \Mpdf\Mpdf(['format' => [264, 225]]);
    $mpdf->SetHeader("Umpire List - ".time()." ");
    $mpdf->SetFooter("© Aflua | Page {PAGENO}");
    $mpdf->WriteHTML($html);
    $mpdf->Output("umpire-list-".time().".pdf", \Mpdf\Output\Destination::DOWNLOAD);
?>
