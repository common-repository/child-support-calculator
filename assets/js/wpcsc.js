jQuery.fn.validateNumerals = function () {
    if (false == jQuery.isNumeric(this.val())) {
        this.val(0);
    }
    if (this.val() < 0 || this.val() == undefined)
        this.val(0);
}

jQuery(document).ready(function ($) {

    let salaryTotal = new Array(800, 850, 900, 950, 1000, 1050, 1100, 1150, 1200, 1250, 1300, 1350, 1400, 1450, 1500, 1550, 1600, 1650, 1700, 1750, 1800, 1850, 1900, 1950, 2000, 2050, 2100, 2150, 2200, 2250, 2300, 2350, 2400, 2450, 2500, 2550, 2600, 2650, 2700, 2750, 2800, 2850, 2900, 2950, 3000, 3050, 3100, 3150, 3200, 3250, 3300, 3350, 3400, 3450, 3500, 3550, 3600, 3650, 3700, 3750, 3800, 3850, 3900, 3950, 4000, 4050, 4100, 4150, 4200, 4250, 4300, 4350, 4400, 4450, 4500, 4550, 4600, 4650, 4700, 4750, 4800, 4850, 4900, 4950, 5000, 5050, 5100, 5150, 5200, 5250, 5300, 5350, 5400, 5450, 5500, 5550, 5600, 5650, 5700, 5750, 5800, 5850, 5900, 5950, 6000, 6050, 6100, 6150, 6200, 6250, 6300, 6350, 6400, 6450, 6500, 6550, 6600, 6650, 6700, 6750, 6800, 6850, 6900, 6950, 7000, 7050, 7100, 7150, 7200, 7250, 7300, 7350, 7400, 7450, 7500, 7550, 7600, 7650, 7700, 7750, 7800, 7850, 7900, 7950, 8000, 8050, 8100, 8150, 8200, 8250, 8300, 8350, 8400, 8450, 8500, 8550, 8600, 8650, 8700, 8750, 8800, 8850, 8900, 8950, 9000, 9050, 9100, 9150, 9200, 9250, 9300, 9350, 9400, 9450, 9500, 9550, 9600, 9650, 9700, 9750, 9800, 9850, 8900, 9950, 10000);
    let one = new Array(190, 202, 213, 224, 235, 246, 258, 269, 280, 290, 300, 310, 320, 330, 340, 350, 360, 370, 380, 390, 400, 410, 421, 431, 442, 452, 463, 473, 484, 494, 505, 515, 526, 536, 547, 557, 568, 578, 588, 597, 607, 616, 626, 635, 644, 654, 663, 673, 682, 691, 701, 710, 720, 729, 738, 748, 757, 767, 776, 784, 793, 802, 811, 819, 828, 837, 846, 854, 863, 872, 881, 889, 898, 907, 916, 924, 933, 942, 951, 959, 968, 977, 986, 993, 1000, 1006, 1013, 1019, 1025, 1032, 1038, 1045, 1051, 1057, 1064, 107, 1077, 1083, 1089, 1096, 1102, 1107, 1111, 1116, 1121, 1126, 1131, 1136, 1141, 1145, 1150, 1155, 1160, 1165, 1170, 1175, 1179, 1184, 1189, 1193, 1196, 1200, 1204, 1208, 1212, 1216, 1220, 1224, 1228, 1232, 1235, 1239, 1243, 1247, 1251, 1255, 1259, 1263, 1267, 1271, 1274, 1278, 1282, 1286, 1290, 1294, 1298, 1302, 1306, 1310, 1313, 1317, 1321, 1325, 1329, 1333, 1337, 1341, 1345, 1349, 1352, 1356, 1360, 1364, 1368, 1372, 1376, 1380, 1384, 1388, 1391, 1395, 1399, 1403, 1407, 1411, 1415, 1419, 1422, 1425, 1427, 1430, 1432, 1435, 1437);
    let two = new Array(211, 257, 302, 347, 365, 382, 400, 417, 435, 451, 467, 482, 498, 513, 529, 544, 560, 575, 591, 606, 622, 638, 654, 670, 686, 702, 718, 734, 751, 767, 783, 799, 815, 831, 847, 864, 880, 896, 912, 927, 941, 956, 971, 986, 1001, 1016, 1031, 1045, 1060, 1075, 1090, 1105, 1120, 1135, 1149, 1164, 1179, 1194, 1208, 1221, 1234, 1248, 1261, 1275, 1288, 1302, 1315, 1329, 1342, 1355, 1369, 1382, 1396, 1409, 1423, 1436, 1450, 1463, 1477, 1490, 1503, 1517, 1530, 1542, 1551, 1561, 1571, 1580, 1590, 1599, 1609, 1619, 1628, 1638, 1647, 1657, 1667, 1676, 1686, 1695, 1705, 1713, 1721, 1729, 1737, 1746, 1754, 1762, 1770, 1778, 1786, 1795, 1803, 1811, 1819, 1827, 1835, 1843, 1850, 1856, 1862, 1868, 1873, 1879, 1885, 1891, 1897, 1903, 1909, 1915, 1921, 1927, 1933, 1939, 1945, 1951, 1957, 1963, 1969, 1975, 1981, 1987, 1992, 1998, 2004, 2010, 2016, 2022, 2028, 2034, 2040, 2046, 2052, 2058, 2064, 2070, 2076, 2082, 2088, 2094, 2100, 2106, 2111, 2117, 2123, 2129, 2135, 2141, 2147, 2153, 2159, 2165, 2171, 2177, 2183, 2189, 3195, 2201, 2206, 2210, 2213, 2217, 2221, 2225, 2228);
    let three = new Array(213, 259, 305, 351, 397, 443, 489, 522, 544, 565, 584, 603, 623, 642, 662, 681, 701, 720, 740, 759, 779, 798, 818, 839, 859, 879, 899, 919, 940, 960, 980, 1000, 1020, 1041, 1061, 1081, 1101, 1121, 1141, 1160, 1178, 1197, 1215, 1234, 1252, 1271, 1289, 1308, 1327, 1345, 1364, 1382, 1401, 1419, 1438, 1456, 1475, 1493, 1503, 1520, 1536, 1553, 1570, 1587, 1603, 1620, 1637, 1654, 1670, 1687, 1704, 1721, 1737, 1754, 1771, 1788, 1804, 1821, 1838, 1855, 1871, 1888, 1905, 1927, 1939, 1952, 1964, 1976, 1988, 2000, 2012, 2024, 2037, 2049, 2061, 2073, 2085, 2097, 2109, 2122, 2134, 2144, 2155, 2165, 2175, 2185, 2196, 2206, 2216, 2227, 2237, 2247, 2258, 2268, 2278, 2288, 2299, 2309, 2317, 2325, 2332, 2340, 2347, 2355, 2362, 2370, 2378, 2385, 2393, 2400, 2408, 2415, 2423, 2430, 2438, 2446, 2453, 2461, 2468, 2476, 2483, 2491, 2498, 2506, 2513, 2521, 2529, 2536, 2544, 2551, 2559, 2566, 2574, 2581, 2589, 2597, 2604, 2612, 2619, 2627, 2634, 2642, 2649, 2657, 2664, 2672, 2680, 2687, 2695, 2702, 2710, 2717, 2725, 2732, 2740, 2748, 2755, 2763, 2767, 2772, 2776, 2781, 2786, 2791, 2795);
    let four = new Array(216, 262, 309, 355, 402, 448, 495, 541, 588, 634, 659, 681, 702, 724, 746, 768, 790, 812, 833, 855, 877, 900, 923, 946, 968, 991, 101, 1037, 1060, 1082, 1105, 1128, 1151, 1174, 1196, 1219, 1242, 1265, 1287, 1308, 1328, 1349, 1370, 1391, 1412, 1433, 1453, 1474, 1495, 1516, 1537, 1558, 1579, 1599, 1620, 1641, 1662, 1683, 1702, 1721, 1740, 1759, 1778, 1797, 1816, 1835, 1854, 1873, 1892, 1911, 1930, 1949, 1968, 1987, 2006, 2024, 2043, 2062, 2081, 2100, 2119, 2138, 2157, 2174, 2188, 2202, 2215, 2229, 2243, 2256, 2270, 2283, 2297, 2311, 2324, 2338, 2352, 2365, 2379, 2393, 2406, 2418, 2429, 2440, 2451, 2462, 2473, 2484, 2495, 2506, 2517, 2529, 2540, 2551, 2562, 2573, 2584, 2595, 2604, 2613, 2621, 2630, 2639, 2647, 2656, 2664, 2673, 2681, 2690, 2698, 2707, 2716, 2724, 2733, 2741, 2750, 2758, 2767, 2775, 2784, 2792, 2801, 2810, 2818, 2827, 2835, 2844, 2852, 2861, 2869, 2878, 2887, 2895, 2904, 2912, 2921, 2929, 2938, 2946, 2955, 2963, 2972, 2981, 2989, 2998, 3006, 3015, 3023, 3032, 3040, 3049, 3058, 3066, 3075, 3083, 3092, 3100, 3109, 3115, 3121, 3126, 3132, 3137, 3143, 3148);
    let five = new Array(218, 265, 312, 359, 406, 453, 500, 547, 594, 641, 688, 735, 765, 789, 813, 836, 860, 884, 907, 931, 955, 979, 1004, 1029, 1054, 1079, 1104, 1129, 1154, 1179, 1204, 1229, 1254, 1279, 1304, 1329, 1354, 1379, 1403, 1426, 1448, 1471, 1494, 1517, 1540, 1563, 1586, 1608, 1631, 1654, 1677, 1700, 1723, 1745, 1768, 1791, 1814, 1837, 1857, 1878, 1899, 1920, 1940, 1961, 1982, 2002, 2023, 2044, 2064, 2085, 2106, 2127, 2147, 2168, 2189, 2209, 2230, 2251, 2271, 2292, 2313, 2334, 2354, 2372, 2387, 2402, 2417, 2432, 2447, 2462, 2477, 2492, 2507, 2522, 2537, 2552, 2567, 2582, 2597, 2612, 2627, 2639, 2651, 2663, 2676, 2688, 2700, 2712, 2724, 2737, 2749, 2761, 2773, 2785, 2798, 2810, 2822, 2834, 2845, 2854, 2863, 2872, 2882, 2891, 2900, 2909, 2919, 2928, 2937, 2946, 2956, 2965, 2974, 2983, 2993, 3002, 3011, 3020, 3030, 3039, 3048, 3057, 3067, 3076, 3085, 3094, 3104, 3113, 3122, 3131, 3141, 3150, 3159, 3168, 3178, 3187, 3196, 3205, 3215, 3224, 3233, 3242, 3252, 3261, 3270, 3279, 3289, 3298, 3307, 3316, 3326, 3335, 3344, 3353, 3363, 3372, 3381, 3390, 3396, 3402, 3408, 3414, 3420, 3426, 3432);
    let six = new Array(220, 268, 315, 363, 410, 458, 505, 553, 600, 648, 695, 743, 790, 838, 869, 895, 920, 945, 971, 996, 1022, 1048, 1074, 1101, 1128, 1154, 1181, 1207, 1234, 1261, 1287, 1314, 1340, 137, 1394, 1420, 1447, 1473, 1500, 1524, 1549, 1573, 1598, 1622, 1647, 1671, 1695, 1720, 1744, 1769, 1793, 1818, 1842, 1867, 1891, 1915, 1940, 1964, 1987, 2009, 2031, 2053, 2075, 2097, 2119, 2141, 2163, 2185, 2207, 2229, 2251, 2273, 2295, 2317, 2339, 2361, 2384, 2406, 2428, 2450, 2472, 2494, 2516, 2535, 2551, 267, 2583, 2599, 2615, 2631, 2647, 2663, 2679, 2695, 2711, 2727, 2743, 2759, 2775, 2791, 2807, 2820, 2833, 2847, 2860, 2874, 2887, 2900, 2914, 2927, 2941, 2954, 2967, 2981, 2994, 3008, 3021, 3034, 3045, 3055, 3064, 3074, 3084, 3094, 3103, 3113, 3123, 3133, 3142, 3152, 3162, 3172, 3181, 3191, 3201, 3211, 3220, 3230, 3240, 3250, 3259, 3269, 3279, 3289, 3298, 3308, 3318, 3328, 3337, 3347, 3357, 3367, 3376, 3386, 3396, 3406, 3415, 3425, 3435, 3445, 3454, 3464, 3474, 3484, 3493, 3503, 3513, 3523, 3532, 3542, 3552, 3562, 3571, 3581, 3591, 3601, 3610, 3620, 3628, 3634, 3641, 3647, 3653, 3659, 3666);

    let childrenObj = { 'one' : 1, 'two' : 2, 'three' : 3, 'four' : 4, 'five' : 5, 'six' : 6};

    jQuery(document).on('change', '.wpcsc-form-group input, .wpcsc-form-group select', function(e) {
       jQuery(this).removeClass('invalid');
    });

    jQuery(document).on('submit', '.form-ajax', function(e) {
        e.preventDefault();
        let parentForm = jQuery(this);

        let btnClicked = parentForm.find('input[type=submit]');
        let parentCalc = parentForm.closest('.wpcsc-calc');
        let resultArea = parentCalc.find(".result-area");
        let cscFormRows = parentForm.find('div.csc-form-rows');
        let spinner = parentForm.find('.dashicons-update');

        let action = ( undefined !== parentForm.data('action') ) ? parentForm.data('action') : 'action_ajax_handler';

        let form_data = new FormData(parentForm[0]);
        form_data.append('action', action);

        // if ( window.wpcsc.can_use_premium_code ) {
        // 	alert('good');
        // }

        if(form_data.get('handle') === null){
            let handle = ( undefined !== parentForm.data('handle') ) ? parentForm.data('handle') : 'wpcsc';
            form_data.append('handle', handle);
        }
        if(form_data.get('task') === null){
            let task = ( undefined !== parentForm.data('task') ) ? parentForm.data('task') : 'add';
            form_data.append('task', task);
        }

        parentForm.find(':input').prop("disabled", true);
        resultArea.removeClass('alert alert-success alert-danger').html('');
        btnClicked.attr('disabled', true);
        spinner.addClass('wpcsc-spin').removeClass('d-none');

        jQuery.ajax({
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            url: WPCSC_AJAX.ajaxurl,
            timeout: 10000,
        }).done(
            function(response) {
                parentForm.find(':input').prop("disabled", false);
                spinner.removeClass('wpcsc-spin').addClass('d-none');
                btnClicked.attr('disabled', false);

                if( response.success === true ) {
                    resultArea.removeClass('d-none').addClass('alert alert-success').html(response.data.reason);
                    cscFormRows.html('');
                }else {
                    resultArea.removeClass('d-none').addClass('alert alert-danger').html(response.data.reason);
                }
            }
        ).fail(
            function( response ) {
                parentForm.find(':input').prop("disabled", false);
                spinner.removeClass('wpcsc-spin').addClass('d-none');
                btnClicked.attr('disabled', false);
            }
        );
    });

    jQuery(".wpcsc-form").submit(function (e) {
        e.preventDefault();

        let $parentForm = jQuery(this);
        let $btnClicked = $parentForm.find('.wpcsc-start');
        let $parentCalc = $btnClicked.closest('.wpcsc');
        let resultArea = $parentCalc.find(".result-area");
        //alert(jQuery(this).attr('id'));

        resultArea.html('<div class="wpcsc-box text-center"><i class="dashicons dashicons-update wpcsc-spin" style="margin:20px"></i></div>');

        // SET ALL VALUES
        let no_of_children = $parentCalc.find(".no_of_children");
        let your_income = $parentCalc.find(".your_income");
        let your_overnights = $parentCalc.find(".your_overnights");
        let spouse_income = $parentCalc.find(".spouse_income");

        let no_of_childrenValue = no_of_children.val();
        let your_incomeValue = Math.round(your_income.val());
        let your_overnightsValue = Math.round(your_overnights.val());
        let spouse_incomeValue = Math.round(spouse_income.val());

        // console.log("Children: " + no_of_childrenValue);

        let yourIncomeShare = your_incomeValue / (your_incomeValue + spouse_incomeValue);
        let spouseIncomeShare = 1-yourIncomeShare;
        // console.log("Your Income Share: " + yourIncomeShare);

        // totalMonthlyIncome
        let totalMonthlyIncome = Math.round(your_incomeValue + spouse_incomeValue);
        // console.log("TotalIncome: " + totalMonthlyIncome);

        let quotient = Math.round(totalMonthlyIncome/50);
        totalMonthlyIncome = 50 * quotient;

        if(totalMonthlyIncome > 10000) {
            totalMonthlyIncome = 10000;
        }

        let matchedIncomeSlab = jQuery.inArray(totalMonthlyIncome, salaryTotal);
        let htmlResult = '<div class="wpcsc-row wpcsc-box wpcsc-export-box">';

        if (matchedIncomeSlab === -1 && totalMonthlyIncome < 800) {
            htmlResult += '<span class="text-danger">Court will determine support amount only if combined income is less than $800.</span>';
        } else {
            let monthlyChildSupportTotal = 0;

            // console.log("matchedIncomeSlab: " + matchedIncomeSlab);

            let children = 0;
            switch (no_of_childrenValue) {
                case "one":
                    monthlyChildSupportTotal = one[matchedIncomeSlab];
                    children = 1;
                    break;
                case "two":
                    monthlyChildSupportTotal = two[matchedIncomeSlab];
                    children = 2;
                    break;
                case "three":
                    monthlyChildSupportTotal = three[matchedIncomeSlab];
                    children = 3;
                    break;
                case "four":
                    monthlyChildSupportTotal = four[matchedIncomeSlab];
                    children = 4;
                    break;
                case "five":
                    monthlyChildSupportTotal = five[matchedIncomeSlab];
                    children = 5;
                    break;
                case "six":
                    monthlyChildSupportTotal = six[matchedIncomeSlab];
                    children = 6;
                    break;
            }

            // console.log("monthlyChildSupportTotal: " + monthlyChildSupportTotal);

            let yourLiability = (monthlyChildSupportTotal * yourIncomeShare);
            let spouseLiability = (monthlyChildSupportTotal * spouseIncomeShare);

            let yourOvernightsShare = your_overnightsValue / 365;
            yourOvernightsShare = your_overnightsValue / 100;
            let spouseOvernightsShare = (1-yourOvernightsShare);

            if (your_overnightsValue < 20) {

            } else {
                console.log("Liability Before: " + yourLiability + " Spouse: " + spouseLiability);

                monthlyChildSupportTotal = Math.round(monthlyChildSupportTotal * 1.5);
                yourLiability = Math.round(monthlyChildSupportTotal * yourIncomeShare);
                spouseLiability = Math.round(monthlyChildSupportTotal * spouseIncomeShare);
                // spouseLiability = monthlyChildSupportTotal - yourLiability;

                console.log("Liability: " + yourLiability + " Spouse: " + spouseLiability);

                yourLiability = Math.round(yourLiability * spouseOvernightsShare);
                // spouseLiability = monthlyChildSupportTotal - yourLiability;
                spouseLiability = Math.round(spouseLiability * yourOvernightsShare);
            }

            yourLiability = parseInt(yourLiability);
            spouseLiability = parseInt(spouseLiability);

            //alert("Liability: " + yourLiability + " Spouse: " + spouseLiability);

            // let object = {
            //     'a_yourIncomeShare' : yourIncomeShare,
            //     'b_spouseIncomeShare' : spouseIncomeShare,
            //     'c_yourOvernightsShare' : yourOvernightsShare,
            //     'd_spouseOvernightsShare' : spouseOvernightsShare,
            //
            //     'e_monthlyChildSupportTotal' : monthlyChildSupportTotal,
            //     'f_yourLiability' : yourLiability,
            //     'g_spouseLiability' : spouseLiability,
            // }

            // console.log(object);

            let result = "";
            let compensation = 0;
            let payOrReceive = '';

            if (spouseLiability >= yourLiability) {
                payOrReceive = 'Receive';
                result = (payOrReceive + " <strong>$" + (spouseLiability - yourLiability)) + "</strong>";
                compensation = (spouseLiability - yourLiability);
            } else {
                payOrReceive = 'Pay';
                result = (payOrReceive + " <strong>$" + (yourLiability - spouseLiability)) + "</strong>";
                compensation = (yourLiability - spouseLiability);
            }

            // result += "<br>" + "Liability: $" + yourLiability + "<br> Spouse: $" + spouseLiability;
            htmlResult += '<div class="wpcsc-col-6">' +
                '<span class="wpcsc-liability d-block">Your Liability: <strong>$' + yourLiability + '</strong></span>' +
                '<span class="wpcsc-spouse-liability d-block">Spouse Liability: <strong>$' + spouseLiability + '</strong></span>' +
                '<span class="wpcsc-compensation d-block">You Should ' + result + '</span>' +
                '</div>' +
                '<div class="wpcsc-col-6 text-end" style="margin-top:35px;">' +
                '<button class="wpcsc-btn wpcsc-export" type="button">Export Result</button>' +
                '</div>' +
                '<div class="wpcsc-col-12 wpcsc-export-area" style="display: none">' +
                '<div class="wpcsc-divider"></div>' +
                '<h5>' + WPCSC_AJAX.export_form_title + '</h5>' +
                '<span class="text-muted">' + WPCSC_AJAX.export_form_text + '</span>' +
                '<form class="wpcsc-export-form" style="margin-top: 15px">' +
                '<div class="wpcsc-form-group wpcsc-row">' +
                '<label for="your_name" class="wpcsc-col-sm-12 wpcsc-col-4">Your Name<i style="color:red">*</i></label>' +
                '<div class="wpcsc-col-sm-12 wpcsc-col-8">' +
                '<input type="text" name="your_name" class="wpcsc-col-8 wpcsc-form-control" required>' +
                '</div>' +
                '</div>' +
                '<div class="wpcsc-form-group wpcsc-row">' +
                '<label for="your_email" class="wpcsc-col-sm-12 wpcsc-col-4">Your Email<i style="color:red">*</i></label>' +
                '<div class="wpcsc-col-sm-12 wpcsc-col-8">' +
                '<input type="email" name="your_email" class="wpcsc-form-control" required>' +
                '</div>' +
                '</div>' +
                '<div class="wpcsc-form-group wpcsc-row">' +
                '<label for="spouse_name" class="wpcsc-col-sm-12 wpcsc-col-4">Spouse Name</label>' +
                '<div class="wpcsc-col-sm-12 wpcsc-col-8">' +
                '<input type="text" name="spouse_name" class="wpcsc-form-control">' +
                '</div>' +
                '</div>' +
                '<div class="wpcsc-form-group wpcsc-row">' +
                '<label for="spouse_email" class="wpcsc-col-sm-12 wpcsc-col-4">Spouse Email</label>' +
                '<div class="wpcsc-col-sm-12 wpcsc-col-8">' +
                '<input type="email" name="spouse_email" class="wpcsc-form-control">' +
                '</div>' +
                '</div>' +
                '<div class="wpcsc-form-group wpcsc-row">' +
                '<div class="wpcsc-col-sm-12 wpcsc-col-12 text-end">' +
                '<input type="hidden" name="pay_or_receive" value="' + payOrReceive + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="children" value="' + children + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="your_income" value="' + your_incomeValue + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="spouse_income" value="' + spouse_incomeValue + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="over_nights" value="' + your_overnightsValue + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="compensation" value="' + compensation + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="liability" value="' + yourLiability + '" class="wpcsc-form-control">' +
                '<input type="hidden" name="spouse_liability" value="' + spouseLiability + '" class="wpcsc-form-control">' +
                '<button type="submit" class="wpcsc-btn"><i class="dashicons dashicons-update d-none"></i> Export</button>' +
                '</div>' +
                '</div>' +
                '<div class="wpcsc-form-group wpcsc-row">' +
                '<div class="wpcsc-col-12 ajax-result"></div>' +
                '</div>' +
                '</form>' +
                '</div>';
        }

        htmlResult += '</div>'; /* End export-box container */

        setTimeout(function () {
            resultArea.html(htmlResult);
        }, 1000);
    });

    jQuery(document).on('click', '.wpcsc-export', function () {

        let btnClicked = jQuery(this);
        let resultArea = btnClicked.closest('.wpcsc-export-box');
        let exportArea = resultArea.find('.wpcsc-export-area');
        if (exportArea.hasClass('wpcsc-form-opened')) {
            exportArea.slideUp();
            exportArea.removeClass('wpcsc-form-opened');
        } else {
            exportArea.addClass('wpcsc-form-opened');
            exportArea.slideDown();
        }
    });

    jQuery(document).on('click', '.wpcsc-export-directly', function(e) {
       jQuery('form.wpcsc-export-form').submit();
    });

    jQuery(document).on('submit', 'form.wpcsc-export-form', function (e) {
        e.preventDefault();
        let parentForm = jQuery(this);
        let btnClicked = parentForm.find('button[type=submit]');
        btnClicked.attr('disabled', true);

        let ajaxResult = parentForm.find('div.ajax-result');
        ajaxResult.removeClass('wpcsc-alert wpcsc-success wpcsc-danger');
        ajaxResult.html('');
        let spinner = parentForm.find('i.dashicons-update');
        spinner.removeClass('d-none').addClass('wpcsc-spin');

        let data = new FormData(parentForm[0]);
        // data.append('action', 'wpcsc_export_result');

        parentForm.find(':input').prop("disabled", true);
        jQuery.ajax({
            url: WPCSC_AJAX.ajaxurl,
            data: data,
            type: 'post',
            contentType: false,
            processData: false,
        }).done(function (response) {
            spinner.removeClass('wpcsc-spin').addClass('d-none');
            btnClicked.attr('disabled', false);
            parentForm.find(':input').prop("disabled", false);
            if (response.success === true) {
                ajaxResult.addClass('wpcsc-alert wpcsc-success').html(response.data.reason);
                jQuery('.wpcsc-export-pdf').click();
            } else {
                ajaxResult.addClass('wpcsc-alert wpcsc-danger').html(response.data.reason);
            }
        }).fail(function (response) {
            spinner.removeClass('wpcsc-spin').addClass('d-none');
            btnClicked.attr('disabled', false);
            parentForm.find(':input').prop("disabled", false);
        });
    });

    jQuery(document).on('change', ".wpcsc-nc-form select", function(e) {
       validateForm();
    });

    jQuery(document).on('change', '.no_of_children', function () {
        let noOfChildren = jQuery(this).val();
        let overNightSelector = jQuery('.wpcsc-on');

        let children = childrenObj[noOfChildren];
        let overNightsHtml = '';

        for(let i = 0; i < children; i++){
            overNightsHtml += '<div class="wpcsc-form-group" style="margin-right:3px">' +
                '<label for="over_nights">Enter the number of overnights that you spend with child '+ (i+1) +'</label>' +
                '<input type="number" min="0" max="365" name="over_nights[]" class="wpcsc-form-control wpcsc-over-nights" value="" placeholder="Enter value">' +
                '<span class="wpcsc-error-msg"></span>' +
            '</div>';
        }
        overNightSelector.find('div.csc-over-nights').html(overNightsHtml);
    });
});

function showTab(n) {
    // This function will display the specified tab of the form ...
    const x = document.getElementsByClassName("wpcsc-tab");
    x[n].style.display = "block";

    let nxtBtn = jQuery('.wpcsc-nxt-btn');
    let prevBtn = jQuery('.wpcsc-prev-btn');

    if (n === 0) {
        prevBtn.find('button').css('display', 'none');
    } else {
        prevBtn.find('button').css('display', 'inline');
    }
    if (n === (x.length - 1)) {
        nxtBtn.html('<button class="wpcsc-btn wpcsc-start"><i class="dashicons dashicons-update d-none" style="margin-top:3px"></i> Submit</button>');
    } else {
        nxtBtn.html('<button class="wpcsc-btn wpcsc-start" type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>');
    }

    fixStepIndicator(n)
}

function nextPrev(n) {
    // This function will figure out which tab to display
    const x = document.getElementsByClassName("wpcsc-tab");
    // Exit the function if any field in the current tab is invalid:
    if (n === 1 && !validateForm()) return false;

    // Hide the current tab:
    x[currentTab].style.display = "none";

    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;

    // if you have reached the end of the form... :
    if (currentTab >= x.length) {
        //...the form gets submitted:
        jQuery(this).submit();
        return false;
    }

    // Otherwise, display the correct tab:
    showTab(currentTab);
}

function validateForm() {
    // This function deals with validation of the form fields
    let x, y, i, valid = true;
    x = document.getElementsByClassName("wpcsc-tab");
    y = x[currentTab].getElementsByTagName("input");

    let parentForm = jQuery(x[0]).parents().eq(1);
    let disabledZeros = parentForm.hasClass('disabled-zeros');

    // A loop that checks every input field in the current tab:
    for (i = 0; i < y.length; i++) {

        let currentInput = jQuery(y[i]);
        let currentInputErrorMsg = currentInput.parent().find('.wpcsc-error-msg');
        let currentValue = currentInput.val();

        currentInputErrorMsg.hide();

        // If a field is empty...
        if (currentValue === "" && disabledZeros === false) {
            // add an "invalid" class to the field:
            currentInput.addClass("invalid");
            currentInputErrorMsg.html("Please enter a value").show();
            // and set the current valid status to false:
            valid = false;
        }

        if(currentInput.hasClass("wpcsc-over-nights")) {
            if(currentValue < 0 || currentValue > 365) {
                currentInput.addClass('invalid');                
                valid = false;
                currentInputErrorMsg.html("Please enter a value between 0 and 365").show();
            }
        }

        if(currentInput.hasClass("gross_income")) {

            let totalValue = 0;
            let letCurrentTab = jQuery(x[currentTab]);
            let currentGrossIncomes = letCurrentTab.find('input');

            currentGrossIncomes.each(function(item) {
                if(jQuery(this).val() != "")
                    totalValue += parseInt(jQuery(this).val());
            });

            if(totalValue==0) {
                currentGrossIncomes.each(function(item) {
                    if(jQuery(this).val()==0) {
                        jQuery(this).addClass('invalid');
                    }
                });

                // alert("One of the partners should have a valid gross income.");
                valid = false;
            }
        }
    }
    
    y = x[currentTab].getElementsByTagName("select");

    // A loop that checks every input field in the current tab:
    for (i = 0; i < y.length; i++) {
        let currentInput = jQuery(y[i]);
        let currentInputErrorMsg = currentInput.parent().find('.wpcsc-error-msg');
        currentInputErrorMsg.hide();

        // If a field is empty...
        if (currentInput.val()=="") {
            // add an "invalid" class to the field:
            currentInput.addClass("invalid");
            currentInputErrorMsg.html("Please choose an option");
            currentInputErrorMsg.show();
            // and set the current valid status to false:
            valid = false;
        }

    }
    
    // If the valid status is true, mark the step as finished and valid:
    if (valid) {
        document.getElementsByClassName("wpcsc-step")[currentTab].className += " finish";
    }

    return valid; // return the valid status
}

function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    let i, x = document.getElementsByClassName("wpcsc-step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class to the current step:
    x[n].className += " active";
}