<?php
session_start();



if (!isset($_COOKIE['auth_token']) && !verify_access($_SERVER['PHP_SELF'])) {
    //header("Location: ./login.php");
    //exit;
    if ($_SERVER['SCRIPT_URL'] !== '/fingerprint.php' && $_SERVER['PHP_SELF'] !== '/kestrel/fingerprint.php') {
        header("Location: https://en.wikipedia.org/wiki/Mind_your_own_business?err=".$_SERVER['REQUEST_URI']);
        exit;
    }
} elseif ($_SERVER['PHP_SELF'] == '/kestrel/fingerprint.php' || $_SERVER['PHP_SELF'] == '/fingerprint.php') {
    if (isset($_COOKIE['auth_token']) && verify_access($_SERVER['PHP_SELF'])) {
        header("Location: ./login.php?err=p2");
        exit;
    }
}

if (isset($_SESSION['token'])) {
    if (!isset($TokenVerificationExeception)) {
        $tokenVerif = json_decode(httpGet('https://kever.io/finder_17.php', [], ['Cookie: PHPSESSID=7d8j381hsqv050c9ai6i4of0aq; authToken=' . $_SESSION['token'] . '; visitorId=973ad0dd0c565ca2ae839d5ebef8447a']), true);
        if (isset($tokenVerif['success'])) {
            if ($tokenVerif['success'] !== true) {
                header("Location: ./logout.php");
                exit;
            }
        } else {
            header("Location: ./logout.php");
            exit;
        }
    }
} else {
    if (!isset($TokenVerificationExeception)) {
        header("Location: ./logout.php");
        exit;
    }
}


use simplehtmldom\HtmlDocument;

require 'vendor/autoload.php';



$httpClient = new \simplehtmldom\HtmlWeb();



function scrape_2($data)
{
    $st2 = (new HtmlDocument())->load($data);
    $data = array();
    $data['kra_pin'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table tr td.whitepapartdBig', 0)->plaintext);
    $data['full_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table tr td.whitepapartdBig', 1)->plaintext);
    $data['citizenship'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#indiCitizenship td.whitepapartdBig', 0)->plaintext);
    $data['major_group'] = strstr(strip_tags($st2->find('div.panelGridHead tr#page1 table tr td fieldset td.whitepapartdBig', 1)->plaintext), '-');
    $data['sub_group'] = strstr(strip_tags($st2->find('div.panelGridHead tr#page1 table tr td fieldset td.whitepapartdBig', 2)->plaintext), '-');
    $data['minor_group'] = strstr(strip_tags($st2->find('div.panelGridHead tr#page1 table tr td fieldset td.whitepapartdBig', 3)->plaintext), '-');

    $data['id_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 0)->plaintext);
    $data['id_issue_date'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 1)->plaintext);
    $data['id_issue_place'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 2)->plaintext);
    $data['nssf_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 4)->plaintext);
    $data['first_name_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 5)->plaintext);
    $data['middle_name_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 6)->plaintext);
    $data['last_name_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 7)->plaintext);
    $data['dob_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 8)->plaintext);
    $data['place_of_birth'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 9)->plaintext);
    $data['sex_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 10)->plaintext);
    $data['marital_status'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 11)->plaintext);
    $data['father_id_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 12)->plaintext);
    $data['father_first_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 13)->plaintext);
    $data['father_middle_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 14)->plaintext);
    $data['father_last_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 15)->plaintext);
    $data['father_place_of_birth'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 17)->plaintext);
    $data['father_dob'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 16)->plaintext);
    $data['mother_id_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 18)->plaintext);
    $data['mother_first_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 19)->plaintext);
    $data['mother_middle_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 20)->plaintext);
    $data['mother_last_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 21)->plaintext);
    $data['mother_place_of_birth'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 23)->plaintext);
    $data['mother_dob'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 22)->plaintext);
    $data['spouse_id_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 24)->plaintext);
    $data['spouse_first_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 25)->plaintext);
    $data['spouse_middle_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 26)->plaintext);
    $data['spouse_last_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 27)->plaintext);
    $data['spouse_dob'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 28)->plaintext);
    $data['spouse_place_of_birth'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan td.whitepapartdBig', 29)->plaintext);
    $data['lr_no_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 0)->plaintext);
    $data['building_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 1)->plaintext);
    $data['street_road_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 2)->plaintext);
    $data['city_town_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 3)->plaintext);
    $data['county_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 4)->plaintext);
    $data['district_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 5)->plaintext);
    $data['area_locality_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 6)->plaintext);
    $data['descriptive_address_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr td.whitepapartdBig', 6)->plaintext);
    $data['postal_code_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr tr#paRow1 td.whitepapartdBig', 0)->plaintext);
    $data['postal_town'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr tr#paRow1 td.whitepapartdBig', 1)->plaintext);
    $data['po_box_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocResidentAddr tr#paRow1 td.whitepapartdBig', 2)->plaintext);
    $data['address_line_4'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocNonResidentAddr table.tab3 td.whitepapartdBig', 1)->plaintext);
    $data['address_line_5'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocNonResidentAddr table.tab3 td.whitepapartdBig', 3)->plaintext);
    $data['address_line_6'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocNonResidentAddr table.tab3 td.whitepapartdBig', 5)->plaintext);

    //$data['country_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE div#txtTxprKELocNonResidentAddr td.whitepapartdBig option[selected]', 7)->plaintext);
    $data['telephone_number'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE fieldset table[class!=tab3] tr[!id] td.whitepapartdBig', 8)->plaintext);
    $data['mobile_number'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE fieldset table[class!=tab3] tr[!id] td.whitepapartdBig', 9)->plaintext);
    $data['mobile_number_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE fieldset table[class!=tab3] tr[!id] td.whitepapartdBig', 10)->plaintext);
    $data['mobile_number_3'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE fieldset table[class!=tab3] tr[!id] td.whitepapartdBig', 11)->plaintext);
    $data['main_email_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE fieldset table[class!=tab3] tr[!id] td.whitepapartdBig', 12)->plaintext);
    $data['secondary_email_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#kenyan div#addContactKE fieldset table[class!=tab3] tr[!id] td.whitepapartdBig', 13)->plaintext);

    $data['nssf_no_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 1)->plaintext);
    $data['alien_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 2)->plaintext);
    $data['first_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 3)->plaintext);
    $data['middle_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 4)->plaintext);
    $data['last_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 5)->plaintext);
    $data['origin_country'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 6)->plaintext);
    $data['work_permit'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 7)->plaintext);
    $data['sex'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 8)->plaintext);
    $data['dob'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan td.whitepapartdBig', 9)->plaintext);
    $data['lr_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow1 td.whitepapartdBig', 0)->plaintext);
    $data['building'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow1 td.whitepapartdBig', 1)->plaintext);
    $data['street_road'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow2 td.whitepapartdBig', 0)->plaintext);
    $data['city_town'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow2 td.whitepapartdBig', 1)->plaintext);
    $data['county'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow3 td.whitepapartdBig', 0)->plaintext);
    $data['district'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow3 td.whitepapartdBig', 1)->plaintext);
    $data['tax_area'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow4 td.whitepapartdBig', 0)->plaintext);
    $data['descriptive_address'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#pfaRow4 td.whitepapartdBig', 1)->plaintext);
    $data['postal_code'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#paRow1 td.whitepapartdBig', 0)->plaintext);
    $data['town'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#paRow1 td.whitepapartdBig', 1)->plaintext);
    $data['po_box'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyan div#addContactNKE tr#paRow1 td.whitepapartdBig', 2)->plaintext);

    $data['first_name_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 0)->plaintext);
    $data['middle_name_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 1)->plaintext);
    $data['last_name_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 2)->plaintext);
    $data['dob_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 3)->plaintext);
    $data['sex_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 4)->plaintext);
    $data['passport_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 5)->plaintext);
    $data['passport_issue_country'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 6)->plaintext);
    $data['passport_issue_date'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 7)->plaintext);
    $data['passport_expiry_date'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 8)->plaintext);
    $data['address_line_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 9)->plaintext);
    $data['address_line_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 10)->plaintext);
    $data['address_line_3'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 11)->plaintext);
    $data['country'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 12)->plaintext);
    $data['telephone_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 13)->plaintext);
    $data['mobile_no_1'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 14)->plaintext);
    $data['mobile_no_2'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 15)->plaintext);
    $data['mobile_no_3'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 16)->plaintext);
    $data['main_email'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 17)->plaintext);
    $data['secondary_email'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div#nonKenyanNonResi td.whitepapartdBig', 18)->plaintext);

    $data['sms_notifications'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td div fieldset#smsSectionIndi td.whitepapartdBig', 0)->plaintext);

    $data['alt_lr_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 0)->plaintext);
    $data['alt_building'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 1)->plaintext);
    $data['alt_street_road'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 2)->plaintext);
    $data['alt_city_town'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 3)->plaintext);
    $data['alt_county'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 4)->plaintext);
    $data['alt_district'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 5)->plaintext);
    $data['alt_tax_area'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 6)->plaintext);
    $data['alt_descriptive_address'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 7)->plaintext);
    $data['alt_postal_code'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 8)->plaintext);
    $data['alt_town'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 9)->plaintext);
    $data['alt_po_box'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#txtAltAdResidentAddr td.whitepapartdBig', 10)->plaintext);

    $data['alt_telephone_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#alterAddDtls fieldset td.whitepapartdBig', 0)->plaintext);
    $data['alt_mobile_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#alterAddDtls fieldset td.whitepapartdBig', 1)->plaintext);
    $data['alt_email'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldstAlter div#alterAddDtls fieldset td.whitepapartdBig', 2)->plaintext);

    $data['bank_declaration'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldStBank fieldset td.whitepapartdBig', 0)->plaintext);

    $data['bank_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldStBank div#divBankAccountDtls td.whitepapartdBig', 0)->plaintext);
    $data['bank_branch_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldStBank div#divBankAccountDtls td.whitepapartdBig', 1)->plaintext);
    $data['bank_city'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldStBank div#divBankAccountDtls td.whitepapartdBig', 2)->plaintext);
    $data['bank_acc_holder_name'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldStBank div#divBankAccountDtls td.whitepapartdBig', 3)->plaintext);
    $data['bank_acc_no'] = strip_tags($st2->find('div.panelGridHead tr#page1 table tr td table div#fldStBank div#divBankAccountDtls td.whitepapartdBig', 4)->plaintext);

    $data['tax_obligation'] = strip_tags($st2->find('div.panelGridHead tr#page2 tr tbody tr.whitepapartdBig td', 0)->plaintext);
    $dt_auth = $st2->find('div.panelGridHead tr#page2 tr tbody tr.whitepapartdBig td', 1);
    if (isset($dt_auth->plaintext)) {
        $data['tax_reg_date'] = strip_tags($st2->find('div.panelGridHead tr#page2 tr tbody tr.whitepapartdBig td', 1)->plaintext);
        $data['itax_rollout_date'] = strip_tags($st2->find('div.panelGridHead tr#page2 tr tbody tr.whitepapartdBig td', 2)->plaintext);
    }

    //Page 3 skipped -- section for Partnership, Corporate and Trusts Information dtls -- section Source of Income and Type of Business Activity data

    //Page 4 skipped -- section Partnership, Corporate and Trusts Information data

    //Page 5 skipped -- section Agent authorized to submit tax returns on behalf taxpayers

    return $data;
}

function httpPost($url, $data, $headers = null)
{
    try {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception('failed to initialize');
        }
        if (is_array($data)) {
            $format_data = http_build_query($data);
        } else {
            $format_data = $data;
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $format_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($headers != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
    } catch (Exception $e) {

        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    } finally {
        // Close curl handle unless it failed to initialize
        if (is_resource($ch)) {
            curl_close($ch);
        }
    }

    return $response;
}

function httpGet($url, $data, $headers = null)
{
    try {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception('failed to initialize');
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($headers != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
    } catch (Exception $e) {

        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    } finally {
        // Close curl handle unless it failed to initialize
        if (is_resource($ch)) {
            curl_close($ch);
        }
    }

    return $response;
}

function httpUpload($url, $data, $headers = null)
{
    /*
    $data = [
        "curr_password" => "2405",
        "email" => "waroruaalex@tsavo.store",
        "firstname" => "Alex",
        "lastname" => "Waroruaa",
        "password" => '$2y$10$eLBwu6e0.SIkFya2eW8KNONGuyH3EkdsfLEF3FdWEMQyui5TKV2Fm',
        "photo" => curl_file_create($fname, 'image/jpg', 'receipt.jpg'),
        "save" => "",
    ];
    // */
    try {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception('failed to initialize');
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($headers != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
    } catch (Exception $e) {

        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    } finally {
        // Close curl handle unless it failed to initialize
        if (is_resource($ch)) {
            curl_close($ch);
        }
    }

    return $response;
}


function FetchNHIFData($id_number)
{
    $url = 'https://nhifapi.tilil.co.ke/api_getprofile';

    $data = '{"id_number":"' . $id_number . '","source":"APP","type":"1"}';
    $nhif_dt = httpPost($url, $data);

    return $nhif_dt;
}

function generateAccessToken()
{
    $data = [
        "username" => "bombardier.devs.master@gmail.com",
        "password" => "Godfrey2405&#",
        "grant_type" => "password",
        "scope" => "user.avatar user.info user.assert user.update"
    ];
    $data = json_encode($data);

    $gt1 = json_decode(httpPost('https://accounts.ecitizen.go.ke/oauth/access-token', $data, ['Content-Type: application/json', 'Authorization: Basic ZGNmYzMyMzkzNjY1MmE5MjZmZmM5YzQ0ZGFjZDQ3ZDc6WTVaYW50N2Jkei80czlLQ1lzVy9pejY2Z3dwN1p6d3hLbUNiSVhML2V5ND0=', 'User-Agent: Dart/3.4 (dart:io)']), true);
    if (is_array($gt1)) {
        if (isset($gt1['access_token'])) {
            return $gt1['access_token'];
        } else {
            //$object_1['kra'] = 'KRA PIN Not available for Identity Provided!';
            //echo 'Error generating Access Token: '.json_encode($gt1);
            sendSSEMessage('error', 'Error generating Access Token: ' . json_encode($gt1));
        }
    } else {
        //echo 'Error generating Access Token: '.json_encode($gt1);
        sendSSEMessage('error', 'Error generating Access Token: ' . json_encode($gt1));
    }
}

function checkRole($requiredRole)
{
    session_start(); // Ensure session is started
    if ($_SESSION['role_id'] != $requiredRole) {
        header('Location: error.php'); // Redirect unauthorized users
        exit();
    }
}
function validateRole($requiredRole)
{
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != $requiredRole) {
        header('Location: error.php'); // Redirect unauthorized access
        exit();
    }
}


function createCustomFile($filePath, $data, $encryptionKey = 'JleUprWVhSaElqcDdJbWRsZEVOMWMzUnZiV1Z5U1c1bWJ5STZleUptYVhKemRFNWhiV1VpT2lKS2IyaHVJaXdpYkdGemRFNWhiV1VpT2lKRWIyVWlMQ0pwWkU1MWJXSmxjaUk2SWpNeU5URTFOVEl5SW4xOWZRPT0iLCJRdWVyeU15TnVtYmVycyI6ImV5SmtZWFJoSWpwN0luRjFaWEo1VFhsT2RXMWlaWEp6SWpwYmV5SmpiR1ZoY2lJNklqQTNNakl3TURBd01EQWlMQ0p6ZEdGMGRYTWlPaUpoWTNScGRtVWlmU3g3SW1Oc1pXRnlJam9pTURjek16Q')
{
    //$filePath = str_replace('/', '-', $filePath);
    $filePath = str_replace('@', '-', $filePath);
    // Encrypt the data
    $encryptedData = openssl_encrypt(
        $data,
        'AES-256-CBC',
        $encryptionKey,
        0,
        substr(hash('sha256', $encryptionKey), 0, 16) // Generate IV
    );

    // Save to custom file
    file_put_contents($filePath, $encryptedData);
}

// Define custom file path and content
// $filePath = 'data.myext'; // Your custom file type
// $data = "This is secret data only readable by my system!";
// $encryptionKey = 'my-secret-key'; // Replace with a strong key

// createCustomFile($filePath, $data, $encryptionKey);
// echo "Custom file created: $filePath\n";


function readCustomFile($filePath, $encryptionKey = 'JleUprWVhSaElqcDdJbWRsZEVOMWMzUnZiV1Z5U1c1bWJ5STZleUptYVhKemRFNWhiV1VpT2lKS2IyaHVJaXdpYkdGemRFNWhiV1VpT2lKRWIyVWlMQ0pwWkU1MWJXSmxjaUk2SWpNeU5URTFOVEl5SW4xOWZRPT0iLCJRdWVyeU15TnVtYmVycyI6ImV5SmtZWFJoSWpwN0luRjFaWEo1VFhsT2RXMWlaWEp6SWpwYmV5SmpiR1ZoY2lJNklqQTNNakl3TURBd01EQWlMQ0p6ZEdGMGRYTWlPaUpoWTNScGRtVWlmU3g3SW1Oc1pXRnlJam9pTURjek16Q')
{
    // Read the encrypted data
    $encryptedData = file_get_contents($filePath);

    // Decrypt the data
    $decryptedData = openssl_decrypt(
        $encryptedData,
        'AES-256-CBC',
        $encryptionKey,
        0,
        substr(hash('sha256', $encryptionKey), 0, 16) // Generate IV
    );

    return $decryptedData;
}

// Define the custom file path and encryption key
// $filePath = 'data.myext'; // Your custom file type
// $encryptionKey = 'my-secret-key'; // Replace with the same key used for encryption

// $data = readCustomFile($filePath, $encryptionKey);
// echo "Decrypted data: $data\n";

function saveSearch($user_id, $query, $results, $type = 'N/A')
{
    global $conn; // Ensure $conn is accessible globally

    try {
        $stmt = $conn->prepare("INSERT INTO saved_searches (user_id, search_query, results, `type`) VALUES (:user_id, :query, :results, :type)");
        $stmt->execute([
            'user_id' => $user_id,
            'query' => $query,
            'results' => $results,
            'type' => $type
        ]);
        return true; // Indicate success
    } catch (PDOException $e) {
        // Log the error or handle it appropriately
        //error_log("Failed to save search: " . $e->getMessage());
        return "Failed to log search: " . $e->getMessage(); // Indicate failure
    }
}

function generateKraCert($file_path)
{
    $data = json_decode(base64_decode(readCustomFile($file_path)), true);

    if (!$data || !isset($data['kraPortal'])) {
        die("Invalid or missing KRA data.");
    }

    // Extract relevant data for the KRA certificate
    $regiDate = $data['kraPortal']['tax_reg_date'] ?? 'N/A';
    $pin = $data['kraPortal']['kra_pin'] ?? 'N/A';
    $name = $data['kraPortal']['full_name'] ?? 'N/A';
    $email = $data['kraPortal']['main_email_1'] ?? 'N/A';
    $lr = $data['kraPortal']['lr_no_1'] ?? 'N/A';
    $building = $data['kraPortal']['building_1'] ?? 'N/A';
    $road = $data['kraPortal']['street_road_1'] ?? 'N/A';
    $town = $data['kraPortal']['city_town_1'] ?? 'N/A';
    $county = $data['kraPortal']['county_1'] ?? 'N/A';
    $district = $data['kraPortal']['district_1'] ?? 'N/A';
    $locality = $data['kraPortal']['area_locality_1'] ?? 'N/A';
    $postalTown = $data['kraPortal']['postal_town'] ?? 'N/A';
    $box = $data['kraPortal']['po_box_1'] ?? 'N/A';
    $postalCode = $data['kraPortal']['postal_code_1'] ?? 'N/A';
    $obligation = $data['kraPortal']['tax_obligation'] ?? 'N/A';

    // Use your provided HTML template
    $htmlContent = '
    <!DOCTYPE html>
    <html lang="en" style="scrollbar-gutter: stable;" class="h-full font-lexend">
    <head>
    <title>receipt</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        h1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 14pt;
        }

        .a,
        a {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8pt;
        }

        h2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8pt;
        }

        .s1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        .s2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9pt;
        }

        .s3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 10pt;
        }

        .s4 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
        }

        .s5 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10pt;
        }

        p {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: 600;
            text-decoration: none;
            font-size: 10pt;
            margin: 0pt;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }

        body {
            margin: auto;
            width: 700px;
            padding: 50px;
        }

        .flex-container {
            padding: 0;
            margin: 0;
            list-style: none;

            -ms-box-orient: horizontal;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -moz-flex;
            display: -webkit-flex;
            /* display: flex; */
            float: left;
            width: 100%;
        }

        .center {
            -webkit-align-items: center;
            align-items: center;
        }

        .flex-item {
            padding: 5px;
            margin: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <div class="flex-container center">
        <div class="flex-item" style="width: 30%; align-items: flex-start;">
            <p style="text-indent: 0pt;text-align: left; margin-top: 15px;"><span><img width="100%" height="54" alt="image" src="./elements/receipt_files/Image_001.jpg" /></span></p>
        </div>
        <div class="flex-item" style="width: 39%; background-color: rgb(167, 167, 167); height: 55px; font-weight: 900;">
            <p style="text-indent: 0pt;text-align: left;"><br /></p>
            <h1 style="text-indent: 0pt">PIN Certificate</h1>
            <p style="text-indent: 0pt;text-align: left;" />
        </div>
        <div class="flex-item" style="width: 30%; font-weight: 1000;">
            <h2 style="padding-top: 4pt;padding-left: 18pt;text-indent: -8pt;text-align: justify;text-align: right;">For
                General Tax Questions<br />
                Contact KRA Call Centre <br />Tel: +254 (020) 4999 999</h2>
            <h2 style="text-indent: 0pt;text-align: right;">Cell: +254(0711)099 999</h2>
            <p style="text-indent: 0pt;text-align: right;"><a href="mailto:callcentre@kra.go.ke" class="a" target="_blank">Email:
                </a><a href="mailto:callcentre@kra.go.ke" target="_blank">callcentre@kra.go.ke</a>
            </p>
        </div>
    </div>
    <br />
    <p style="text-indent: 0pt;text-align: left;"><a href="http://www.kra.go.ke/">www.kra.go.ke</a></p>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"><span><img width="717" height="1" alt="image" src="./elements/receipt_files/Image_002.png" /></span></p>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <h2 style="padding-top: 4pt;text-indent: 0pt;text-align: right;">Certificate Date : <span class="s1">' . $data['kraPortal']['tax_reg_date'] . '</span>
    </h2>
    <h2 style="padding-top: 4pt;text-indent: 0pt;text-align: right;">Personal Identification Number</h2>
    <p class="s1" style="padding-top: 4pt;text-indent: 0pt;text-align: right;">' . $data['kraPortal']['kra_pin'] . '</p>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <p style="padding-left: 5pt;text-indent: 0pt;line-height: 3pt;text-align: left;"><span><img width="715" height="4" alt="image" src="./elements/receipt_files/Image_003.png" /></span></p>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <p class="s2" style="padding-top: 7pt;padding-left: 78pt;text-indent: 0pt;text-align: center;">This is to certify
        that taxpayer shown herein has been registered with Kenya Revenue Authority</p>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <h1 style="padding-top: 7pt;padding-left: 78pt;text-indent: 0pt;text-align: center;">Taxpayer Information</h1>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <table style="border-collapse:collapse;margin-left:22pt" cellspacing="0">
        <tr style="height:17pt">
            <td style="width:198pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 2pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Taxpayer
                    Name</p>
            </td>
            <td style="width:296pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s4" style="padding-top: 2pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">' . $data['kraPortal']['full_name'] . '</p>
            </td>
        </tr>
        <tr style="height:17pt">
            <td style="width:198pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 2pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Email
                    Address</p>
            </td>
            <td style="width:296pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p style="padding-top: 2pt;padding-left: 1pt;text-indent: 0pt;text-align: left;"><a href="mailto:' . $data['kraPortal']['main_email_1'] . '" class="s5">' . $data['kraPortal']['main_email_1'] . '</a></p>
            </td>
        </tr>
    </table>
    <h1 style="padding-top: 7pt;padding-left: 78pt;text-indent: 0pt;text-align: center;">Registered Address</h1>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <table style="border-collapse:collapse;margin-left:22pt" cellspacing="0">
        <tr style="height:18pt">
            <td style="width:249pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">L.R. Number
                    :' . $data['kraPortal']['lr_no_1'] . '</p>
            </td>
            <td style="width:245pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Building :
                    <span class="s4">' . $data['kraPortal']['building_1'] . '</span>
                </p>
            </td>
        </tr>
        <tr style="height:18pt">
            <td style="width:249pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Street/Road
                    : <span class="s4">' . $data['kraPortal']['street_road_1'] . '</span></p>
            </td>
            <td style="width:245pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 3pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">City/Town :
                    <span class="s4">' . $data['kraPortal']['city_town_1'] . '</span>
                </p>
            </td>
        </tr>
        <tr style="height:18pt">
            <td style="width:249pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">County :
                    <span class="s4">' . $data['kraPortal']['county_1'] . '</span>
                </p>
            </td>
            <td style="width:245pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">District :
                    <span class="s4">' . $data['kraPortal']['district_1'] . '</span>
                </p>
            </td>
        </tr>
        <tr style="height:18pt">
            <td style="width:249pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Tax Area :
                    <span class="s4">' . $data['kraPortal']['area_locality_1'] . '</span>
                </p>
            </td>
            <td style="width:245pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Station :
                    <span class="s4">' . $data['kraPortal']['postal_town'] . '</span>
                </p>
            </td>
        </tr>
        <tr style="height:18pt">
            <td style="width:249pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">P. O. Box :
                    <span class="s4">' . $data['kraPortal']['po_box_1'] . '</span>
                </p>
            </td>
            <td style="width:245pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s3" style="padding-top: 4pt;padding-left: 8pt;text-indent: 0pt;text-align: left;">Postal Code
                    : <span class="s4">' . $data['kraPortal']['postal_code_1'] . '</span></p>
            </td>
        </tr>
    </table>
    <h1 style="padding-top: 11pt;padding-left: 78pt;text-indent: 0pt;text-align: center;">Tax Obligation(s) Registration
        Details</h1>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <table style="border-collapse:collapse;margin-left:22pt" cellspacing="0">
        <tr style="height:16pt">
            <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" bgcolor="#CCCCCC">
                <p class="s3" style="padding-top: 2pt;padding-left: 9pt;padding-right: 8pt;text-indent: 0pt;text-align: center;">
                    Sr. No.</p>
            </td>
            <td style="width:131pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" bgcolor="#CCCCCC">
                <p class="s3" style="padding-top: 2pt;padding-left: 24pt;text-indent: 0pt;text-align: left;">Tax
                    Obligation(s)</p>
            </td>
            <td style="width:136pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" bgcolor="#CCCCCC">
                <p class="s3" style="padding-top: 2pt;padding-left: 20pt;padding-right: 19pt;text-indent: 0pt;text-align: center;">
                    Effective From Date</p>
            </td>
            <td style="width:114pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" bgcolor="#CCCCCC">
                <p class="s3" style="padding-top: 2pt;padding-left: 14pt;padding-right: 13pt;text-indent: 0pt;text-align: center;">
                    Effective Till Date</p>
            </td>
            <td style="width:72pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" bgcolor="#CCCCCC">
                <p class="s3" style="padding-top: 2pt;padding-left: 20pt;padding-right: 19pt;text-indent: 0pt;text-align: center;">
                    Status</p>
            </td>
        </tr>
        <tr style="height:22pt">
            <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s4" style="padding-top: 5pt;padding-left: 1pt;text-indent: 0pt;text-align: center;">1</p>
            </td>
            <td style="width:131pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s4" style="padding-left: 14pt;padding-right: 13pt;text-indent: 0pt;line-height: 11pt;text-align: center;">
                ' . $data['kraPortal']['tax_obligation'] . '</p>
                <p class="s4" style="padding-left: 14pt;padding-right: 13pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                    Individual</p>
            </td>
            <td style="width:136pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s4" style="padding-top: 5pt;padding-left: 20pt;padding-right: 19pt;text-indent: 0pt;text-align: center;">
                ' . $data['kraPortal']['tax_reg_date'] . '</p>
            </td>
            <td style="width:114pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s4" style="padding-top: 5pt;padding-left: 14pt;padding-right: 13pt;text-indent: 0pt;text-align: center;">
                    N.A.</p>
            </td>
            <td style="width:72pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s4" style="padding-top: 5pt;padding-left: 20pt;padding-right: 19pt;text-indent: 0pt;text-align: center;">
                    Active</p>
            </td>
        </tr>
    </table>
    <p style="text-indent: 0pt;text-align: left;"><br /></p>

    <p style="padding-left: 26pt;text-indent: 0pt;text-align: justify;">The above PIN must appear on all your tax
        invoices and correspondences with Kenya Revenue Authority. Your accounting end date is 31st December as per the
        provisions stated in the Income Tax Act unless a change has been approved by the Commissioner-Domestic Taxes
        Department.The status of Tax Obligation(s) with &#39;Dormant’ status will automatically change to
        &#39;Active&#39; on date mentioned in &quot;Effective Till Date&quot; or any transaction done during the period.
        This certificate shall remain in force till further updated.</p>
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <p style="text-indent: 0pt;text-align: left;"><br /></p>
    <h2 style="padding-bottom: 3pt;padding-left: 38pt;text-indent: 0pt;text-align: left;">Disclaimer : <span class="s1">This is a system generated certificate and does not require signature.</span></h2>
    <p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"><span><img width="706" height="1" alt="image" src="./elements/receipt_files/Image_004.png" /></span></p>
</body>

</html>';

    // Render the HTML in a new window
    return "<script>
        var newWindow = window.open('', '_blank');
        newWindow.document.open();
        newWindow.document.write(`" . addslashes($htmlContent) . "`);
        newWindow.document.close();
    </script>";
}

function generateIdCard($file_path)
{
    $data = json_decode(base64_decode(readCustomFile($file_path)), true);

    if (!$data || !isset($data['iprs'])) {
        die("Invalid or missing National ID data.");
    }

    // Extract relevant data for the National ID
    $fullName = $data['iprs']['first_name'] . ' ' . $data['iprs']['middle_name'] . ' ' . $data['iprs']['sur_name'];
    $idNumber = $data['iprs']['nid_no'];
    $dateOfBirth = $data['iprs']['birth_dt'] ?? '-';
    $gender = $data['iprs']['gender'] ?? '-';
    $placeOfIssue = $data['iprs']['nid_issue_place'] ?? '-';
    $dateOfIssue = $data['iprs']['nid_issue_dt'] ?? '-';
    $districtOfBirth = $data['iprs']['district'] ?? '-';
    $serialNumber = $data['iprs']['serial_number'] ?? '-';

    // Generate the HTML content
    return "<!DOCTYPE html>
    <html lang='en' style='scrollbar-gutter: stable;' class='h-full font-lexend'>
    <head>
        <link href='https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
        <link href='https://fonts.cdnfonts.com/css/better-grade' rel='stylesheet'>
        <link rel='stylesheet' href='https://accounts.ecitizen.go.ke/en/assets/app.css'>
        <script type='text/javascript' src='https://accounts.ecitizen.go.ke/en/assets/app.js'></script>
        <script>
            window.onload = function() {
                window.print(); // Trigger the print dialog
                setTimeout(() => { window.close(); }, 1000); // Automatically close the tab after printing
            }
        </script>
    </head>
    <body>
        <div class='mt-6 sm:mt-14 sm:max-w-xl sm:mx-auto w-full sm:overflow-hidden overflow-auto'>
            <div class='z-10 relative overflow-hidden shadow-sm h-full sm:h-[329px] w-[542px] rounded-lg py-8 px-6'>
                <div class='-z-10 absolute inset-0'>
                    <img class='h-full w-full' src='https://accounts.ecitizen.go.ke/en/images/national_ID.png' alt='National ID background image'>
                </div>
                <div class='grid grid-cols-5 gap-4'>
                    <div class='mt-2.5 col-span-2 text-right'>
                        <p class='text-base text-[#546350] font-extrabold'>JAMUHURI YA KENYA</p>
                        <span class='block mt-1.5 text-xs text-gray-900 font-normal'>
                            Serial Number: <span class='text-sm font-bold'>{$serialNumber}</span>
                        </span>
                    </div>
                    <div class='col-span-1 h-16 w-auto'>
                        <img class='h-full w-full' src='https://accounts.ecitizen.go.ke/en/images/coa.svg' alt='Court of arms'>
                    </div>
                    <div class='mt-2.5 col-span-2 text-left'>
                        <p class='text-base text-[#546350] font-extrabold'>REPUBLIC OF KENYA</p>
                        <span class='block mt-1.5 text-xs text-gray-900 font-normal'>
                            ID Number: <span class='text-sm font-bold'>{$idNumber}</span>
                        </span>
                    </div>
                </div>
                <div class='mt-3.5 grid grid-cols-6 gap-4'>
                    <div class='col-span-2'>
                        <img class='h-full w-full aspect-square' src='https://accounts.ecitizen.go.ke/en/images/m_ID_avatar.svg' alt='M Avatar image'>
                    </div>
                    <div class='col-span-3'>
                        <div>
                            <p class='text-xs font-medium'>Full name</p>
                            <span class='text-sm font-bold'>{$fullName}</span>
                        </div>
                        <dl class='mt-1 grid grid-cols-2'>
                            <div class='col-span-1'>
                                <dt class='text-xs font-medium leading-5 text-gray-900'>Date of Birth</dt>
                                <dd class='mt-0.5 text-xs leading-5 text-gray-700'>{$dateOfBirth}</dd>
                            </div>
                            <div class='col-span-1'>
                                <dt class='text-xs font-medium leading-5 text-gray-900'>Place of issue</dt>
                                <dd class='mt-0.5 text-xs leading-5 text-gray-700'>{$placeOfIssue}</dd>
                            </div>
                            <div class='col-span-1'>
                                <dt class='text-xs font-medium leading-5 text-gray-900'>Gender</dt>
                                <dd class='mt-0.5 text-xs leading-5 text-gray-700'>{$gender}</dd>
                            </div>
                            <div class='col-span-1'>
                                <dt class='text-xs font-medium leading-5 text-gray-900'>Date of Issue</dt>
                                <dd class='mt-0.5 text-xs leading-5 text-gray-700'>{$dateOfIssue}</dd>
                            </div>
                            <div class='col-span-1'>
                                <dt class='text-xs font-medium leading-5 text-gray-900'>District of birth</dt>
                                <dd class='mt-0.5 text-xs leading-5 text-gray-700'>{$districtOfBirth}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

function logSearch($userId, $searchType, $query, $result)
{
    global $conn; // Database connection (PDO)
    $query = "INSERT INTO search_logs (user_id, search_type, query, result, created_at) VALUES (:user_id, :search_type, :query, :result, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':search_type', $searchType, PDO::PARAM_STR);
    $stmt->bindValue(':query', $query, PDO::PARAM_STR);
    $stmt->bindValue(':result', $result, PDO::PARAM_STR);
    $stmt->execute();
}

function reverseToBase64($obj)
{
    // Rebuild the CustomerInfo object
    $CustomerInfo = [
        'data' => [
            'getCustomerInfo' => [
                'firstName' => $obj['firstName'],
                'lastName'  => $obj['lastName'],
                'idNumber'  => $obj['idNumber'],
            ],
        ],
    ];

    // Encode CustomerInfo to base64
    $encodedCustomerInfo = base64_encode(json_encode($CustomerInfo));

    // Rebuild the MyNumbers object
    $MyNumbers = [
        'data' => [
            'queryMyNumbers' => array_map(function ($number) {
                return [
                    'clear'  => $number['number'],
                    'status' => $number['status'],
                ];
            }, $obj['myNumbers']),
        ],
    ];

    // Encode MyNumbers to base64
    $encodedMyNumbers = base64_encode(json_encode($MyNumbers));

    // Rebuild the responseData object
    $responseData = [
        'GetCustomerInfo' => $encodedCustomerInfo,
        'QueryMyNumbers'  => $encodedMyNumbers,
    ];

    // Encode the final responseData to base64
    $finalBase64 = base64_encode(json_encode($responseData));

    return $finalBase64;
}

function processKestrelFile($filePath)
{
    try {
        // Decrypt using existing custom method
        $encryptedData = file_get_contents($filePath);
        $decryptedData = readCustomFile($filePath);
        $base64Decoded = base64_decode($decryptedData);

        // Validate JSON structure
        $data = json_decode($base64Decoded, true, 512, JSON_THROW_ON_ERROR);

        // Security audit trail
        saveSearch($_SESSION['user_id'], "Kestrel File Processed", $filePath, "KESTREL_DECRYPT");

        return $data;
    } catch (Exception $e) {
        error_log("Kestrel processing failed: " . $e->getMessage());
        return false;
    }
}


function checkUserRole($requiredPermission)
{
    // Implement your actual RBAC logic here
    //return in_array($_SESSION['role'], getPermissionsForUser($_SESSION['user_id']));
    return true;
}

function getJsonPaths($data, $prefix = '')
{
    $paths = [];
    foreach ($data as $key => $value) {
        $currentPath = $prefix ? "$prefix.$key" : $key;

        if (is_array($value) || is_object($value)) {
            $paths = array_merge($paths, getJsonPaths((array)$value, $currentPath));
        } else {
            // Handle array indices
            if (preg_match('/\.(\d+)$/', $currentPath, $matches)) {
                $currentPath = preg_replace('/\.\d+$', '[*]', $currentPath);
            }
            $paths[] = $currentPath;
        }
    }
    return array_unique($paths);
}

function activityLog($user_id, $action, $results, $type = 'N/A')
{
    global $conn; // Ensure $conn is accessible globally

    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, `action`, results, `type`) VALUES (:user_id, :action, :results, :type)");
        $stmt->execute([
            'user_id' => $user_id,
            'action' => $action,
            'results' => $results,
            'type' => $type
        ]);
        return true; // Indicate success
    } catch (PDOException $e) {
        // Log the error or handle it appropriately
        //error_log("Failed to save search: " . $e->getMessage());
        return "Failed to log search: " . $e->getMessage(); // Indicate failure
    }
}

function advancedActivityLog($action, $response = '', $typeOverride = null)
{
    // Only log POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    global $conn;  // Ensure $conn is accessible globally
    $user_id = $_SESSION['user_id'] ?? 0;

    // Determine the HTTP status code
    $statusCode = http_response_code();

    // Default type is "Info" unless we determine success/error
    $logType = 'Info';

    // If a specific type is forced, use it
    if (!empty($typeOverride)) {
        $logType = $typeOverride;
    } else {
        // Otherwise, infer from status code & presence of certain keywords
        if ($statusCode >= 400) {
            $logType = 'Error';
        } elseif (stripos($response, 'error') !== false || stripos($response, 'fail') !== false || stripos($response, 'invalid') !== false) {
            // If "error" is found in $response, consider it an error
            $logType = 'Error';
        } elseif (stripos($response, 'success') !== false) {
            // If "success" is found, consider it success
            $logType = 'Success';
        }
    }

    // Build a short "results" message (limit length to avoid huge DB entries)
    // Includes status code and snippet of $response
    $resultsSnippet = mb_substr($response, 0, 250);
    $results = "HTTP $statusCode - $resultsSnippet";

    // Finally, call your existing activityLog() function
    activityLog($user_id, $action, $results, $logType);
}

function encrypt_token($data, $key='dElwIjoiMTk2LjIwMS4yMTguMTI2Iiwib3MiOiJXaW5kb3dzIDEwLjAiLCJzb3VyY2UiOiJNb')
{
    $key = hash('sha256', $key);
    $iv = openssl_random_pseudo_bytes(16);
    return base64_encode($iv . openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv));
}

function decrypt_token($token, $key='dElwIjoiMTk2LjIwMS4yMTguMTI2Iiwib3MiOiJXaW5kb3dzIDEwLjAiLCJzb3VyY2UiOiJNb')
{
    $data = base64_decode($token);
    $iv = substr($data, 0, 16);
    $key = hash('sha256', $key);
    return openssl_decrypt(substr($data, 16), 'aes-256-cbc', $key, 0, $iv);
}
function generate_device_hash()
{
    $components = [
        $_SERVER['HTTP_USER_AGENT'],
        $_SERVER['HTTP_ACCEPT_LANGUAGE'],
        gethostname(),
        $_SERVER['HTTP_ACCEPT_ENCODING']
    ];
    return hash('sha256', implode('|', $components));
}

// includes/auth_check.php
function verify_access($page)
{
    global $conn;
    if (!isset($_COOKIE['auth_token'])) {
        //header("Location: ./fingerprint.php");
        if ($page !== '/fingerprint.php' && $page !== '/kestrel/fingerprint.php') {
            //header("Location: https://en.wikipedia.org/wiki/Mind_your_own_business?err=p2");
            //exit;
            return false;
        }
    }

    try {
        $systemKey = $_COOKIE['auth_token'];
        $deviceHash = generate_device_hash();
        $authKey = decrypt_token($systemKey);

        $authKey = decrypt_token($systemKey);
        $stmt = $conn->prepare("SELECT * FROM users WHERE systemKey = :systemKey");
        $stmt->execute(['systemKey' => $authKey]);
        $user = $stmt->fetch();


        $stmt = $conn->prepare("SELECT layer1_key FROM security_files  WHERE user_id = ? AND device_hash = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$user['id'], $deviceHash]);
        $storedKey = $stmt->fetchColumn();


        if ($authKey !== decrypt_token($storedKey)) {
            throw new Exception("Key mismatch");
            //throw new Exception("Key mismatch t1:".$authKey.' t2:'.decrypt_token($storedKey));
        }

        return true;
    } catch (Exception $e) {
        clear_auth_cookies();
        //header("Location: ./fingerprint.php?error=".$e->getMessage());
        if ($page !== '/fingerprint.php' && $page !== '/kestrel/fingerprint.php') {
            header("Location: https://en.wikipedia.org/wiki/Mind_your_own_business?err=p3");
            exit;
        }else{
            return true;
        }
    }
}

function clear_auth_cookies()
{
    setcookie('auth_token', '', time() - 3600, '/');
    setcookie('filePath', '', time() - 3600, '/');
}

function login($username, $password)
{
    // Step 1: Authenticate username and password with the API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://kever.io/finder_10_auth.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'username' => $username,
        'password' => $password,
    ]);
    curl_setopt($ch, CURLOPT_COOKIE, "visitorId=973ad0dd0c565ca2ae839d5ebef8447a");

    $response = curl_exec($ch);
    $apiResponse = json_decode($response, true);
    curl_close($ch);

    return $apiResponse;
}
