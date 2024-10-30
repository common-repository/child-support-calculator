<?php

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('enable_remote', true);
$dompdf = new Dompdf($options);
// wpcsc_ajaxy_die($data);
$dompdf->loadHtml($data);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('Legal', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
// return base64_encode($dompdf->stream());

// ToDo: Add WaterMark to PDF
$add_watermark = true;
if(!empty($args['worksheet']))
    $add_watermark = false;
if($add_watermark) {
    // Instantiate canvas instance
    $canvas = $dompdf->getCanvas();

    // Get height and width of page
        $w = $canvas->get_width();
        $h = $canvas->get_height();

    // Specify watermark image
    $wpcsc_options = get_option('wpcsc_settings');
    $imageURL = $wpcsc_options['export']['logo'] ?? wpcsc_plugin_url('/assets/img/icon.png');
    $imgWidth = 400;
    $imgHeight = 400;

    // Set image opacity
    $canvas->set_opacity(.05);

    // Specify horizontal and vertical position
    $x = (($w-$imgWidth)+50);
    $y = (($h-$imgHeight)+50);

    // Add an image to the pdf
    $canvas->image($imageURL, $x, $y, $imgWidth, $imgHeight);
}

// Render the HTML as PDF
$output = $dompdf->output();

// Output the generated PDF to Browser
// $dompdf->save("file.pdf");

return $output;