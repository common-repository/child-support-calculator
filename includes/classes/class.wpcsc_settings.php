<?php

class WPCSC_SETTINGS extends WPCSC_ENTITY
{
    public function get_tabs(){
        return [
            '' => '<i class="dashicons dashicons-layout"></i>',
            'styles' => 'Form Styles',
            'export-pdf' => 'PDF Settings',
            'email' => 'Email Settings',
            'misc' => 'Miscellaneous Options'
        ];
    }

    public function store_table()
    {
        global $wpdb;
        $wpdb->wpcsc_incomes = "{$wpdb->prefix}wpcsc_incomes";

        $salaryTotal = array(800, 850, 900, 950, 1000, 1050, 1100, 1150, 1200, 1250, 1300, 1350, 1400, 1450, 1500, 1550, 1600, 1650, 1700, 1750, 1800, 1850, 1900, 1950, 2000, 2050, 2100, 2150, 2200, 2250, 2300, 2350, 2400, 2450, 2500, 2550, 2600, 2650, 2700, 2750, 2800, 2850, 2900, 2950, 3000, 3050, 3100, 3150, 3200, 3250, 3300, 3350, 3400, 3450, 3500, 3550, 3600, 3650, 3700, 3750, 3800, 3850, 3900, 3950, 4000, 4050, 4100, 4150, 4200, 4250, 4300, 4350, 4400, 4450, 4500, 4550, 4600, 4650, 4700, 4750, 4800, 4850, 4900, 4950, 5000, 5050, 5100, 5150, 5200, 5250, 5300, 5350, 5400, 5450, 5500, 5550, 5600, 5650, 5700, 5750, 5800, 5850, 5900, 5950, 6000, 6050, 6100, 6150, 6200, 6250, 6300, 6350, 6400, 6450, 6500, 6550, 6600, 6650, 6700, 6750, 6800, 6850, 6900, 6950, 7000, 7050, 7100, 7150, 7200, 7250, 7300, 7350, 7400, 7450, 7500, 7550, 7600, 7650, 7700, 7750, 7800, 7850, 7900, 7950, 8000, 8050, 8100, 8150, 8200, 8250, 8300, 8350, 8400, 8450, 8500, 8550, 8600, 8650, 8700, 8750, 8800, 8850, 8900, 8950, 9000, 9050, 9100, 9150, 9200, 9250, 9300, 9350, 9400, 9450, 9500, 9550, 9600, 9650, 9700, 9750, 9800, 9850, 8900, 9950, 10000);
        $one = array(190, 202, 213, 224, 235, 246, 258, 269, 280, 290, 300, 310, 320, 330, 340, 350, 360, 370, 380, 390, 400, 410, 421, 431, 442, 452, 463, 473, 484, 494, 505, 515, 526, 536, 547, 557, 568, 578, 588, 597, 607, 616, 626, 635, 644, 654, 663, 673, 682, 691, 701, 710, 720, 729, 738, 748, 757, 767, 776, 784, 793, 802, 811, 819, 828, 837, 846, 854, 863, 872, 881, 889, 898, 907, 916, 924, 933, 942, 951, 959, 968, 977, 986, 993, 1000, 1006, 1013, 1019, 1025, 1032, 1038, 1045, 1051, 1057, 1064, 107, 1077, 1083, 1089, 1096, 1102, 1107, 1111, 1116, 1121, 1126, 1131, 1136, 1141, 1145, 1150, 1155, 1160, 1165, 1170, 1175, 1179, 1184, 1189, 1193, 1196, 1200, 1204, 1208, 1212, 1216, 1220, 1224, 1228, 1232, 1235, 1239, 1243, 1247, 1251, 1255, 1259, 1263, 1267, 1271, 1274, 1278, 1282, 1286, 1290, 1294, 1298, 1302, 1306, 1310, 1313, 1317, 1321, 1325, 1329, 1333, 1337, 1341, 1345, 1349, 1352, 1356, 1360, 1364, 1368, 1372, 1376, 1380, 1384, 1388, 1391, 1395, 1399, 1403, 1407, 1411, 1415, 1419, 1422, 1425, 1427, 1430, 1432, 1435, 1437);
        $two = array(211, 257, 302, 347, 365, 382, 400, 417, 435, 451, 467, 482, 498, 513, 529, 544, 560, 575, 591, 606, 622, 638, 654, 670, 686, 702, 718, 734, 751, 767, 783, 799, 815, 831, 847, 864, 880, 896, 912, 927, 941, 956, 971, 986, 1001, 1016, 1031, 1045, 1060, 1075, 1090, 1105, 1120, 1135, 1149, 1164, 1179, 1194, 1208, 1221, 1234, 1248, 1261, 1275, 1288, 1302, 1315, 1329, 1342, 1355, 1369, 1382, 1396, 1409, 1423, 1436, 1450, 1463, 1477, 1490, 1503, 1517, 1530, 1542, 1551, 1561, 1571, 1580, 1590, 1599, 1609, 1619, 1628, 1638, 1647, 1657, 1667, 1676, 1686, 1695, 1705, 1713, 1721, 1729, 1737, 1746, 1754, 1762, 1770, 1778, 1786, 1795, 1803, 1811, 1819, 1827, 1835, 1843, 1850, 1856, 1862, 1868, 1873, 1879, 1885, 1891, 1897, 1903, 1909, 1915, 1921, 1927, 1933, 1939, 1945, 1951, 1957, 1963, 1969, 1975, 1981, 1987, 1992, 1998, 2004, 2010, 2016, 2022, 2028, 2034, 2040, 2046, 2052, 2058, 2064, 2070, 2076, 2082, 2088, 2094, 2100, 2106, 2111, 2117, 2123, 2129, 2135, 2141, 2147, 2153, 2159, 2165, 2171, 2177, 2183, 2189, 3195, 2201, 2206, 2210, 2213, 2217, 2221, 2225, 2228);
        $three = array(213, 259, 305, 351, 397, 443, 489, 522, 544, 565, 584, 603, 623, 642, 662, 681, 701, 720, 740, 759, 779, 798, 818, 839, 859, 879, 899, 919, 940, 960, 980, 1000, 1020, 1041, 1061, 1081, 1101, 1121, 1141, 1160, 1178, 1197, 1215, 1234, 1252, 1271, 1289, 1308, 1327, 1345, 1364, 1382, 1401, 1419, 1438, 1456, 1475, 1493, 1503, 1520, 1536, 1553, 1570, 1587, 1603, 1620, 1637, 1654, 1670, 1687, 1704, 1721, 1737, 1754, 1771, 1788, 1804, 1821, 1838, 1855, 1871, 1888, 1905, 1927, 1939, 1952, 1964, 1976, 1988, 2000, 2012, 2024, 2037, 2049, 2061, 2073, 2085, 2097, 2109, 2122, 2134, 2144, 2155, 2165, 2175, 2185, 2196, 2206, 2216, 2227, 2237, 2247, 2258, 2268, 2278, 2288, 2299, 2309, 2317, 2325, 2332, 2340, 2347, 2355, 2362, 2370, 2378, 2385, 2393, 2400, 2408, 2415, 2423, 2430, 2438, 2446, 2453, 2461, 2468, 2476, 2483, 2491, 2498, 2506, 2513, 2521, 2529, 2536, 2544, 2551, 2559, 2566, 2574, 2581, 2589, 2597, 2604, 2612, 2619, 2627, 2634, 2642, 2649, 2657, 2664, 2672, 2680, 2687, 2695, 2702, 2710, 2717, 2725, 2732, 2740, 2748, 2755, 2763, 2767, 2772, 2776, 2781, 2786, 2791, 2795);
        $four = array(216, 262, 309, 355, 402, 448, 495, 541, 588, 634, 659, 681, 702, 724, 746, 768, 790, 812, 833, 855, 877, 900, 923, 946, 968, 991, 101, 1037, 1060, 1082, 1105, 1128, 1151, 1174, 1196, 1219, 1242, 1265, 1287, 1308, 1328, 1349, 1370, 1391, 1412, 1433, 1453, 1474, 1495, 1516, 1537, 1558, 1579, 1599, 1620, 1641, 1662, 1683, 1702, 1721, 1740, 1759, 1778, 1797, 1816, 1835, 1854, 1873, 1892, 1911, 1930, 1949, 1968, 1987, 2006, 2024, 2043, 2062, 2081, 2100, 2119, 2138, 2157, 2174, 2188, 2202, 2215, 2229, 2243, 2256, 2270, 2283, 2297, 2311, 2324, 2338, 2352, 2365, 2379, 2393, 2406, 2418, 2429, 2440, 2451, 2462, 2473, 2484, 2495, 2506, 2517, 2529, 2540, 2551, 2562, 2573, 2584, 2595, 2604, 2613, 2621, 2630, 2639, 2647, 2656, 2664, 2673, 2681, 2690, 2698, 2707, 2716, 2724, 2733, 2741, 2750, 2758, 2767, 2775, 2784, 2792, 2801, 2810, 2818, 2827, 2835, 2844, 2852, 2861, 2869, 2878, 2887, 2895, 2904, 2912, 2921, 2929, 2938, 2946, 2955, 2963, 2972, 2981, 2989, 2998, 3006, 3015, 3023, 3032, 3040, 3049, 3058, 3066, 3075, 3083, 3092, 3100, 3109, 3115, 3121, 3126, 3132, 3137, 3143, 3148);
        $five = array(218, 265, 312, 359, 406, 453, 500, 547, 594, 641, 688, 735, 765, 789, 813, 836, 860, 884, 907, 931, 955, 979, 1004, 1029, 1054, 1079, 1104, 1129, 1154, 1179, 1204, 1229, 1254, 1279, 1304, 1329, 1354, 1379, 1403, 1426, 1448, 1471, 1494, 1517, 1540, 1563, 1586, 1608, 1631, 1654, 1677, 1700, 1723, 1745, 1768, 1791, 1814, 1837, 1857, 1878, 1899, 1920, 1940, 1961, 1982, 2002, 2023, 2044, 2064, 2085, 2106, 2127, 2147, 2168, 2189, 2209, 2230, 2251, 2271, 2292, 2313, 2334, 2354, 2372, 2387, 2402, 2417, 2432, 2447, 2462, 2477, 2492, 2507, 2522, 2537, 2552, 2567, 2582, 2597, 2612, 2627, 2639, 2651, 2663, 2676, 2688, 2700, 2712, 2724, 2737, 2749, 2761, 2773, 2785, 2798, 2810, 2822, 2834, 2845, 2854, 2863, 2872, 2882, 2891, 2900, 2909, 2919, 2928, 2937, 2946, 2956, 2965, 2974, 2983, 2993, 3002, 3011, 3020, 3030, 3039, 3048, 3057, 3067, 3076, 3085, 3094, 3104, 3113, 3122, 3131, 3141, 3150, 3159, 3168, 3178, 3187, 3196, 3205, 3215, 3224, 3233, 3242, 3252, 3261, 3270, 3279, 3289, 3298, 3307, 3316, 3326, 3335, 3344, 3353, 3363, 3372, 3381, 3390, 3396, 3402, 3408, 3414, 3420, 3426, 3432);
        $six = array(220, 268, 315, 363, 410, 458, 505, 553, 600, 648, 695, 743, 790, 838, 869, 895, 920, 945, 971, 996, 1022, 1048, 1074, 1101, 1128, 1154, 1181, 1207, 1234, 1261, 1287, 1314, 1340, 137, 1394, 1420, 1447, 1473, 1500, 1524, 1549, 1573, 1598, 1622, 1647, 1671, 1695, 1720, 1744, 1769, 1793, 1818, 1842, 1867, 1891, 1915, 1940, 1964, 1987, 2009, 2031, 2053, 2075, 2097, 2119, 2141, 2163, 2185, 2207, 2229, 2251, 2273, 2295, 2317, 2339, 2361, 2384, 2406, 2428, 2450, 2472, 2494, 2516, 2535, 2551, 267, 2583, 2599, 2615, 2631, 2647, 2663, 2679, 2695, 2711, 2727, 2743, 2759, 2775, 2791, 2807, 2820, 2833, 2847, 2860, 2874, 2887, 2900, 2914, 2927, 2941, 2954, 2967, 2981, 2994, 3008, 3021, 3034, 3045, 3055, 3064, 3074, 3084, 3094, 3103, 3113, 3123, 3133, 3142, 3152, 3162, 3172, 3181, 3191, 3201, 3211, 3220, 3230, 3240, 3250, 3259, 3269, 3279, 3289, 3298, 3308, 3318, 3328, 3337, 3347, 3357, 3367, 3376, 3386, 3396, 3406, 3415, 3425, 3435, 3445, 3454, 3464, 3474, 3484, 3493, 3503, 3513, 3523, 3532, 3542, 3552, 3562, 3571, 3581, 3591, 3601, 3610, 3620, 3628, 3634, 3641, 3647, 3653, 3659, 3666);

        $all_data = array();
        $table_html = '';
        for ($i = 0; $i < sizeof($salaryTotal); $i++) {
            $all_data[$salaryTotal[$i]] = array('one' => $one[$i], 'two' => $two[$i], 'three' => $three[$i], 'four' => $four[$i], 'five' => $five[$i], 'six' => $six[$i]);
            $table_html .= '
               <tr>
                    <td title="Combined Salary in USD">' . $salaryTotal[$i] . '</td>
                    <td title="Minimum child support amount for one children will be approx: ' . WPCSC::get_percentage($one[$i], $salaryTotal[$i]) . '%">' . $one[$i] . '</td>
                    <td title="Minimum child support amount for two children will be approx: ' . WPCSC::get_percentage($two[$i], $salaryTotal[$i]) . '%">' . $two[$i] . '</td>
                    <td title="Minimum child support amount for three children will be approx: ' . WPCSC::get_percentage($three[$i], $salaryTotal[$i]) . '%">' . $three[$i] . '</td>
                    <td title="Minimum child support amount for four children will be approx: ' . WPCSC::get_percentage($four[$i], $salaryTotal[$i]) . '%">' . $four[$i] . '</td>
                    <td title="Minimum child support amount for five children will be approx: ' . WPCSC::get_percentage($five[$i], $salaryTotal[$i]) . '%">' . $five[$i] . '</td>
                    <td title="Minimum child support amount for six children will be approx: ' . WPCSC::get_percentage($six[$i], $salaryTotal[$i]) . '%">' . $six[$i] . '</td>
                </tr>';
        }

        $table = '<table class="wpcsc_table">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th>Salary</th>';
        $table .= '<th colspan=6 style="text-align:center;">Children</th>';
        $table .= '</tr>';
        $table .= '<tr>';
        $table .= '<th>Combined</th>';
        $table .= '<th>One</th>';
        $table .= '<th>Two</th>';
        $table .= '<th>Three</th>';
        $table .= '<th>Four</th>';
        $table .= '<th>Five</th>';
        $table .= '<th>Six</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        $table .= $table_html;
        $table .= '</tbody>';
        $table .= '</table>';

        update_option('wpcsc_table_data', $all_data);
        update_option('wpcsc_table_html', $table);

        $wpcsc_incomes = new WPCSC_INCOMES();

        $logs = $wpcsc_incomes->get();
        if(sizeof($logs)) {
            return false;
        }

        foreach($all_data as $k=>$arr) {
            $arr['salary'] = $k;
            $wpcsc_incomes->create($arr);
        }
    }

    public static function get_settings_html($current_tab){
        $html = '';
        switch ($current_tab){
            case 'styles':
                $html .= self::form_settings();
                break;
            case 'email':
                $html .= self::email_settings();
                break;
            case 'export-pdf':
                $html .= self::pdf_settings();
                break;
            case 'misc':
                $html .= self::misc_settings();
                break;
            default:
                $html .= 'Feature Coming Soon';
        }
        return $html;
    }



    public static function get_template($args, $html = '')
    {
        if (!is_array($args) || empty($html)) {
            return 'No content generated';
        }

        $wpcsc_options = get_option('wpcsc__settings');
        $email_settings = get_option('wpcsc__email');

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $wpcsc_options['logo_path'] ?? wpcsc_plugin_url('/assets/img/logo.png'),
            'watermark' => $wpcsc_options['watermark'] ?? wpcsc_plugin_url('/assets/img/logo.png'),
        ];

        $args = wp_parse_args($args, $defaults);

        foreach ($args as $arg => $val) {
            $html = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $html);
        }

        return $html;
    }

    public static function template_for_pdf()
    {
        ob_start(); ?>

        <!DOCTYPE html>
        <html>
        <title>Child Support Estimation</title>
        <body style="font-size:16px;font-family:sans-serif !important;padding-top:0;color:#6e6e6e">

        <?php echo self::template(); ?>

        </body>
        </html>

        <?php
        return ob_get_clean();
    }

    public static function template($state = '')
    {

        ob_start(); ?>
        <div class="body">
            <style>
                table td {
                    border: none;
                    padding: 10px;
                }

                .wpcsc-footer {
                    padding: 15px 10px;
                    border: 1px solid #fff;
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                }
            </style>
            <table style="width:100%">
                <tr>
                    <td style="font-size:11px"><b>WPCSC</b> <span>Child Support Estimation</span></td>
                    <td style="font-size:11px;text-align:right;">{{current_time}}</td>
                </tr>
            </table>
            <div style="margin-top:10px;">
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td><img src="{{logo_path}}" height="80px" width="auto"></td>
                        <td style="font-size:11px;text-align:right">Estimation by <a href="{{site_url}}">{{site_title}}</a></td>
                    </tr>
                    </tbody>
                </table>
                <table style="width:100%">
                    <tbody>
                    <tr>
                        <td style="text-align:center;text-transform:capitalize;font-size:30px;font-weight:bold;">
                            Child Support Estimation
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Child(ren)</td>
                        <td style="text-align:right;font-weight:bold">{{children}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Your Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{income}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent's Estimated Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_income}}</td>
                    </tr>
                    <tr>
                        <?php if($state == ''):?>
                            <td style="width: 65%;">Your Overnights (%)</td>
                            <td style="text-align:right;font-weight:bold">{{over_nights}}%</td>
                        <?php else:?>
                            <td style="width: 65%;">Your Overnights</td>
                            <td style="text-align:right;font-weight:bold">{{over_nights}} Days</td>
                        <?php endif;?>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Your Name</td>
                        <td style="text-align:right;font-weight:bold">{{name}}<br><small>{{your_email}}</small></td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Name</td>
                        <td style="text-align:right;font-weight:bold">{{spouse_name}}<br><small>{{spouse_email}}</small>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <?php if($state != ''):?>
                        <tr>
                            <td style="width: 70%;">Worksheet</td>
                            <td style="text-align:right;font-weight:bold">{{worksheet}}</td>
                        </tr>
                    <?php endif;?>
                    <tr>
                        <td style="width: 65%;">Your Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{liability}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_liability}}</td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 70%;">Estimated monthly child support payment you should
                            <b>{{pay_or_receive}}</b>
                        </td>
                        <td style="text-align:right;font-weight:bold">$ {{compensation}}</td>
                    </tr>
                    </tbody>
                </table>
                <div class="wpcsc-footer" style="margin:70px auto 20px;text-align:center;font-size:11px;font-style:italic;">Disclaimer:
                    Please remember that these calculators are for informational and educational purposes only.
                    Results calculated from: https://wpchildsupport.com/child-support-calculator
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function admin_template($state = '')
    {
        ob_start(); ?>
        <div class="body">
            <style>
                table td {
                    border: none;
                    padding: 10px;
                }
                .wpcsc-footer {
                    padding: 15px 10px;
                    border: 1px solid #fff;
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                }
            </style>
            <table style="width:100%">
                <tr>
                    <td style="font-size:11px"><b>WPCSC</b> <span>Child Support Estimation</span></td>
                    <td style="font-size:11px;text-align:right;">{{current_time}}</td>
                </tr>
            </table>
            <div style="margin-top:10px;">
                <table style="width:100%">
                    <tbody>
                        <tr>
                            <td><img src="{{logo_path}}" height="80px" width="auto"></td>
                            <td style="font-size:11px;text-align:right">Estimation by <a href="{{site_url}}">{{site_title}}</a></td>
                        </tr>
                    </tbody>
                </table>
                <table style="width:100%">
                    <tbody>
                        <tr>
                            <td style="text-align:center;text-transform:capitalize;font-size:30px;font-weight:bold;">Child Support Estimation</td>
                        </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">Child(ren)</td>
                        <td style="text-align:right;font-weight:bold">{{children}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">First Parent Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{income}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent's Estimated Monthly Gross Income</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_income}}</td>
                    </tr>
                    <tr>
                        <?php if($state == ''):?>
                            <td style="width: 65%;">First Parent's Overnights (%)</td>
                            <td style="text-align:right;font-weight:bold">{{over_nights}}%</td>
                        <?php else:?>
                            <td style="width: 65%;">First Parent's Overnights</td>
                            <td style="text-align:right;font-weight:bold">{{over_nights}} Days</td>
                        <?php endif;?>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 65%;">First Parent</td>
                        <td style="text-align:right;font-weight:bold">{{name}}<br><small>{{your_email}}</small></td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Name</td>
                        <td style="text-align:right;font-weight:bold">{{spouse_name}}<br><small>{{spouse_email}}</small>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <?php if($state != ''):?>
                        <tr>
                            <td style="width: 70%;">Cusstody Type</td>
                            <td style="text-align:right;font-weight:bold">{{worksheet}}</td>
                        </tr>
                    <?php endif;?>
                    <tr>
                        <td style="width: 65%;">Your Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{liability}}</td>
                    </tr>
                    <tr>
                        <td style="width: 65%;">Other Parent Liability</td>
                        <td style="text-align:right;font-weight:bold">$ {{spouse_liability}}</td>
                    </tr>
                    </tbody>
                </table>
                <div style="border:1px solid #e7e6e6;margin-top:25px;line-height:1.3;clear:both;"></div>
                <table style="width:100%;margin-top:25px">
                    <tbody>
                    <tr>
                        <td style="width: 70%;">Estimated monthly child support payment <b>First Parent</b> should
                            <b>{{pay_or_receive}}</b>
                        </td>
                        <td style="text-align:right;font-weight:bold">$ {{compensation}}</td>
                    </tr>
                    </tbody>
                </table>
                <div class="wpcsc-footer"
                     style="margin:70px auto 20px;text-align:center;font-size:11px;font-style:italic;">
                    Disclaimer: Please remember that these calculators are for informational and educational purposes
                    only.
                    Results calculated from: https://wpchildsupport.com/child-support-calculator
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function email_user($state = '')
    {
        $key = ($state == '') ? 'wpcsc__email' : 'wpcsc__email-'.$state;
        $option_name = ($state == '') ? 'email' : 'email-'.$state;

        $email_settings = get_option($key, []);
        $settings = get_option('wpcsc__settings', []);

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $settings['logo_path']
        ];

        foreach ($defaults as $arg => $val) {
            $email_settings['body'] = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $email_settings['body']);
        }
        // var_dump($email_settings);

        $html = '<div class="col-md-6 col-editor">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>User Email Settings <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Upgrade Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $is_premium = wpcsc_fs()->is__premium_only();
        $fieldset_disabled = $is_premium ? '' : 'disabled';
        $fieldset_disabled = '';

        $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="'.$option_name.'">';
        $html .= '<fieldset ' . $fieldset_disabled . '>';
        $html .= ' <div class="row form-group p-2">';
        $html .= '<label class="col col-md-3" for="wpcsc_send_mail">Send Email</label>';
        $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
        $checked = (is_array($email_settings) && $email_settings['enable'] == 'on') ? ' checked' : '';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable]" id="wpcsc_send_mail"' . $checked . '>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_subject">Subject</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[subject]" id="" value="' . $email_settings['subject'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        if ($is_premium) {
            $html .= ' <div class="row form-group p-2">';
            $html .= ' <label class="col-md-3" for="wpcsc_mail_body">Body</label>';
            $html .= ' <div class="col-md-9">';
            // ToDo: Link with HTML Preview - CAN BE DONE AT LAST
            ob_start();
            $editor_id = 'wpcsc_mail_body';
            wp_editor($email_settings['body'], $editor_id, [
                'textarea_name' => 'fields[body]',
            ]);
            $html .= ob_get_clean();
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';

        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>';

        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="'.$option_name.'" style="padding-left:10px;"> Reset Defaults</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6 col-preview">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Email Preview [Demo]<h6>';
        $html .= '</div>';
        $html .= '<div class="card-body wpcsc-email-preview">';

        $html .= '<div class="wpcsc-email-default">';
        $html .= '<style>.wpcsc-footer{position:unset!important;}</style>';
        $html .= $email_settings['body'];
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function email_admin($state = '')
    {
        $key = ($state == '') ? 'wpcsc__admin_email' : 'wpcsc__admin_email-'.$state;
        $option_name = ($state == '') ? 'admin_email' : 'admin_email-'.$state;

        $email_settings = get_option($key, []);
        $settings = get_option('wpcsc__settings', []);

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $settings['logo_path']
        ];

        foreach ($defaults as $arg => $val) {
            $email_settings['body'] = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $email_settings['body']);
        }

        $html = '<div class="col-md-6 col-editor">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Admin Email Settings <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Upgrade Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $is_premium = wpcsc_fs()->is__premium_only();
        $fieldset_disabled = $is_premium ? '' : 'disabled';
        $fieldset_disabled = '';
        // $fieldset_disabled = 'disabled';

        $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="'.$option_name.'">';
        $html .= '<fieldset ' . $fieldset_disabled . '>';
        $html .= ' <div class="row form-group p-2">';
        $html .= '<label class="col col-md-3" for="wpcsc_send_mail">Send Email</label>';
        $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
        $checked = (is_array($email_settings) && $email_settings['enable'] == 'on') ? ' checked' : '';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable]" id="wpcsc_send_mail"' . $checked . '>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_recipient">Recipient</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[receiver]" id="" value="' . $email_settings['receiver'] . '" required>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_subject">Subject</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[subject]" id="" value="' . $email_settings['subject'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';

        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>';

        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= '</div>';

        $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="'.$option_name.'" style="padding-left:10px;"> Reset Defaults</span>';
        $html .= '</div>';
        $html .= '</div>';


        $html .= '<div class="col-md-6 col-preview">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Email Preview [Demo]<h6>';
        $html .= '</div>';
        $html .= '<div class="card-body wpcsc-email-preview">';

        $html .= '<div class="wpcsc-email-default">';
        $html .= '<style>.wpcsc-footer{position:unset!important;}</style>';
        $html .= $email_settings['body'];
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function email_settings(){
        $is_admin_email = (!empty($_GET['email']) && $_GET['email']=="admin-email");
        $user_email_class = 'btn-primary';
        $admin_email_class = 'btn-link';
        if($is_admin_email) {
            $user_email_class = 'btn-link';
            $admin_email_class = 'btn-primary';
        }

        $page = sanitize_text_field($_GET['page']);
        $html = "<div class='col-md-12 mt-1'>";
        $html .= '<a href="'.admin_url("admin.php?page={$page}&tab=email").'" class="btn btn-sm '.$user_email_class.'">User Email</a>';
        $html .= '<a href="'.admin_url("admin.php?page={$page}&tab=email&email=admin-email").'" class="btn btn-sm '.$admin_email_class.'">Admin Email</a>';
        $html .= '</div>';

        $html .= ($is_admin_email) ? self::email_admin() : self::email_user();

        return $html;
    }

    public static function pdf_settings()
    {
        $pdf_settings = get_option('wpcsc__pdf', []);
        $settings = get_option('wpcsc__settings', []);

        $defaults = [
            'site_url' => site_url(),
            'site_title' => get_bloginfo('name'),
            'current_time' => current_time('l, M d, Y  H:i a'),
            'logo_path' => $settings['logo_path']
        ];

        foreach ($defaults as $arg => $val) {
            $pdf_settings['body'] = str_replace("{{" . $arg . "}}", ((!empty($val) || $val == 0) ? $val : "-"), $pdf_settings['body']);
        }

        $html = '<div class="col-md-6 col-editor">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>PDF Export Settings <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';

        $html .= '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Buy Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $is_premium = wpcsc_fs()->is__premium_only();
        $fieldset_disabled = $is_premium ? '' : 'disabled';
        $fieldset_disabled = '';

        $html .= '<form class="form-ajax" data-action="action_update_settings" data-task="pdf">';
        $html .= '<fieldset ' . $fieldset_disabled . '>';
        $html .= ' <div class="row form-group p-2">';
        $html .= '<label class="col col-md-3" for="wpcsc_send_mail">Export PDF</label>';
        $html .= '<div class="col-8 col-md-9 form-switch" style="padding-left:calc(var(--bs-gutter-x) * .5)">';
        $checked = (is_array($pdf_settings) && $pdf_settings['enable'] == 'on') ? ' checked' : '';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" name="fields[enable]" id="wpcsc_export_pdf"' . $checked . '>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="wpcsc_recipient">File Name Prefix</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <input type="text" class="form-control" name="fields[file_name_prefix]" id="" value="' . $pdf_settings['file_name_prefix'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        if ($is_premium) {
            $html .= ' <div class="row form-group p-2">';
            $html .= ' <label class="col-md-3" for="wpcsc_mail_body">Body</label>';
            $html .= ' <div class="col-md-9">';
            // ToDo: Link with HTML Preview - CAN BE DONE AT LAST
            ob_start();
            $editor_id = 'wpcsc_mail_body';
            wp_editor($pdf_settings['body'], $editor_id, [
                'textarea_name' => 'fields[body]',
            ]);
            $html .= ob_get_clean();
            // $html .= ' <textarea type="text" class="form-control" name="wpcsc_mail_body" id="wpcsc_mail_body">'.$pdf_settings['wpcsc_mail_body'].'</textarea>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';
        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>';
        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</form>';
        $html .= '</div>';

        $html .= '<span class="btn btn-link text-danger small btn-ajaxy wpcsc-reset-default" data-op="reset_default" data-handle="wpcsc" data-option="pdf" style="padding-left:10px;"> Reset Defaults</span>';
        $html .= '</div>';
        $html .= '</div>';


        $html .= '<div class="col-md-6 col-preview">';
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Email Preview [Demo]<h6>';
        $html .= '</div>';
        $html .= '<div class="card-body wpcsc-email-preview">';
        $html .= '<div class="wpcsc-email-default">';
        $html .= '<style>.wpcsc-footer{position:unset!important;}</style>';
        $html .= $pdf_settings['body'];
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function form_settings(){

        $form_settings = get_option('wpcsc__form', []);
        $select_field_styles = !empty($form_settings['select_field_styles']) ? $form_settings['select_field_styles'] : '';
        $input_field_styles = !empty($form_settings['input_field_styles']) ? $form_settings['input_field_styles'] : '';

        $html = "<div class='col-md-12 col-editor'>";
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Form Styles [Front-End]<h6>';
        $html .= '</div>';
        $html .=  '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Buy Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }
        $html .= '<form class="form-ajax mt-3" data-action="action_update_settings" data-task="form">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<h5 class="badge bg-primary">Calculator Form Settings [Front End]</h5>';
        $html .= '<fieldset>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="form_title">Form Title</label>';
        $html .= ' <div class="col-md-9">';
        $html .= '<textarea class="form-control code" rows="2" name="fields[form_title]" id="form_title">'.($form_settings['form_title'] ?? '').'</textarea>';
        $html .= '<span class="small text-danger">This will be the title in the calculator.</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="form_text">Form Text</label>';
        $html .= ' <div class="col-md-9">';
        $html .= '<textarea class="form-control code" rows="2" name="fields[form_text]" id="form_text">'.($form_settings['form_text'] ?? '').'</textarea>';
        $html .= '<span class="small text-danger">This will be the text displayed below the title.</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<h5 class="badge bg-primary mt-3">Form Styles [Front End]</h5>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="select_field_styles">Select Field Style</label>';
        $html .= ' <div class="col-md-9">';
        $html .= '<textarea class="form-control code" name="fields[select_field_styles]" rows="3" id="select_field_styles">'.$select_field_styles.'</textarea>';
        $html .= '<span class="small text-danger">Please add CSS styles for SELECT field here.</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="input_field_styles">Input Field Style</label>';
        $html .= ' <div class="col-md-9">';
        $html .= ' <textarea class="form-control code" name="fields[input_field_styles]" rows="3" id="input_field_styles">'.$input_field_styles.'</textarea>';
        $html .= '<span class="small text-danger">Please add CSS styles for INPUT field here.</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</fieldset>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<h5 class="badge bg-primary">Export Form Settings [Front End]</h5>';
        $html .= '<fieldset>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="export_form_title">Export Form Title</label>';
        $html .= ' <div class="col-md-9">';
        $html .= '<textarea class="form-control code" rows="2" name="fields[export_form_title]" id="form_title">'.($form_settings['export_form_title'] ?? '').'</textarea>';
        $html .= '<span class="small text-danger">This will be the title in export form.</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= ' <div class="row form-group p-2">';
        $html .= ' <label class="col-md-3" for="export_form_text">Export Form Text</label>';
        $html .= ' <div class="col-md-9">';
        $html .= '<textarea class="form-control code" rows="2" name="fields[export_form_text]" id="export_form_text">'.($form_settings['export_form_text'] ?? '').'</textarea>';
        $html .= '<span class="small text-danger">This will be the text displayed below the title in export form.</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</fieldset>';
        $html .= '</div>';

        $html .= '<div class="col-md-12">';
        $html .= '<div class="row form-group p-2">';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<input type="hidden" name="wp_nonce" value="' . wp_create_nonce() . '">';
        $html .= '<button class="submit button-primary float-end"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>';
        $html .= '</div>';
        $html .= '<div class="col-md-12 p-2">';
        $html .= '<div class="ajax-result"></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function misc_settings(){
        $misc_settings = get_option('wpcsc__misc', []);

        $html = "<div class='col-md-6 col-editor'>";
        $html .= '<div class="wpcsc-card card">';
        $html .= '<div class="card-header">';
        $html .= '<h6>Cleaning Options <small class="float-end cursor expand-handle"><i class="dashicons dashicons-fullscreen-alt"></i></small><h6>';
        $html .= '</div>';
        $html .=  '<div class="card-body">';
        if (wpcsc_fs()->is_not_paying()) {
            $html .= '<section class="wpcsc-is__premium"><h6>' . __('Awesome Premium Features', WPCSC_TXT_DOMAIN);
            $html .= ' <a href="' . wpcsc_fs()->get_upgrade_url() . '">' . __('Buy Now!', WPCSC_TXT_DOMAIN) . '</a></h6>';
            $html .= '</section>';
        }

        $html .= ' <div class="row p-2 mt-3">';
        $html .= ' <label class="col-md-4" for="select_field_styles">Delete Leads</label>';
        $html .= ' <div class="col-md-8">';
        $html .= '<span class="btn btn-primary btn-sm btn-ajaxy" data-title="Remove leads" data-op="ajax_action" data-ajax="clean" data-clean="leads" data-handle="leads">Remove</span>';
        $html .= '<span class="small text-danger d-block">This operation is not reversible</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= ' <div class="row p-2 mt-3">';
        $html .= ' <label class="col-md-4" for="select_field_styles">Delete Store PDF</label>';
        $html .= ' <div class="col-md-8">';
        $html .= '<span class="btn btn-primary btn-sm btn-ajaxy" data-title="Remove All PDF Files" data-op="ajax_action" data-ajax="clean" data-clean="pdf" data-handle="leads">Remove</span>';
        $html .= '<span class="small text-danger d-block">This operation is not reversible</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= ' <div class="row p-2 mt-3">';
        $html .= ' <label class="col-md-4" for="select_field_styles">Delete Export Data</label>';
        $html .= ' <div class="col-md-8">';
        $html .= '<span class="btn btn-primary btn-sm btn-ajaxy" data-title="Clean Export Data" data-op="ajax_action" data-ajax="clean" data-clean="excel" data-handle="leads">Remove</span>';
        $html .= '<span class="small text-danger d-block">This operation is not reversible</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}