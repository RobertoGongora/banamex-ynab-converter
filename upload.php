<?php

function generateCsv($data, $delimiter = ',', $enclosure = '"') {
	$handle = fopen('php://temp', 'r+');
	foreach ($data as $line) {
		fputcsv($handle, $line, $delimiter, $enclosure);
	}
	rewind($handle);
	$contents = '';
	while (!feof($handle)) {
		$contents .= fread($handle, 8192);
	}
	fclose($handle);
	return $contents;
}

if ( isset( $_FILES['banamex'] ) ) {
	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename="' . $_FILES['banamex']['name'] . '.ynab.csv"' );
	$file = fopen( $_FILES['banamex']['tmp_name'], 'r' );

	$i   = 0;
	$csv = [ [ 'Date', 'Payee', 'Memo', 'Amount' ] ];

	while ( ( $line = fgetcsv( $file ) ) !== false ) {
		if ( $i === 0 ) {
			$i ++;
			continue;
		}

		$amount = ( empty( $line[2] ) ) ? floatval( str_replace( ',', '', $line[3] ) ) : floatval( str_replace( ',', '', $line[2] ) ) * -1;

		$csv[] = [ $line[0], '', $line[1], $amount ];
		$i ++;
	}

	echo generateCsv( $csv );


	exit();
}


exit( header( 'Location: /?sent' ) );