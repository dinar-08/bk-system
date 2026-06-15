<?php
return [
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => [
        'DOMPDF_ENABLE_AUTOLOAD' => false,
        'DOMPDF_ENABLE_REMOTE' => true,
        'DOMPDF_ENABLE_CSS_FLOAT' => true,
        'DOMPDF_ENABLE_FONTSUBSETTING' => true,
        'DOMPDF_DEFAULT_MEDIA_TYPE' => 'screen',
        'DOMPDF_DEFAULT_PAPER_SIZE' => 'a4',
        'DOMPDF_DEFAULT_FONT' => 'serif',
        'DOMPDF_DPI' => 96,
        'DOMPDF_ENABLE_PHP' => false,
        'DOMPDF_ENABLE_HTML5PARSER' => true,
    ],
];
