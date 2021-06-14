<?php
	$data_path = "./data/";
	$json_path = $data_path."json/";
	$csv_path = $data_path."csv/";

	function _indexObjects($section, $out_file_ptr){
		if (isset($section["name"])){
			$object = [
				$section["name"],$section["desc"]
			];

			fputcsv($out_file_ptr, $object);

			if (isset($section["inclusionTerm"])){
				if (is_array($section["inclusionTerm"]["note"])){
					foreach ($section["inclusionTerm"]["note"] as $note){
						$object = [
							$section["name"],$section["desc"]." (".$note.")"
						];

						fputcsv($out_file_ptr, $object);
					}
				}
				else {
					$object = [
						$section["name"],$section["desc"]." (".$section["inclusionTerm"]["note"].")"
					];
					
					fputcsv($out_file_ptr, $object);
				}
			}
		}

		return true;
	}

	function convertJsonToCsv($json_input_name, $csv_output_ptr){
		$source = file_get_contents($json_input_name);
		$source = json_decode($source, true);

		$source_data = $source["ICD10CM.tabular"]["chapter"];

		foreach ($source_data as $chapter){
			$sections = $chapter["section"];
			foreach ($sections as $section){
				if (isset($section["diag"])){
					foreach ($section["diag"] as $diags){
						_indexObjects($diags, $csv_output_ptr);


						if (isset($diags["diag"])){
							foreach ($diags["diag"] as $diag){
								_indexObjects($diag, $csv_output_ptr);

								if (isset($diag["diag"])){
									foreach ($diag["diag"] as $_diag){
										_indexObjects($_diag, $csv_output_ptr);

										if (isset($_diag["diag"])){
											foreach ($_diag["diag"] as $_diag){
												_indexObjects($_diag, $csv_output_ptr);

												if (isset($_diag["diag"])){
													foreach ($_diag["diag"] as $_diag){
														_indexObjects($_diag, $csv_output_ptr);
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			echo "Done with ".$chapter["name"].": ".$chapter["desc"]."\n";
		}
	}

	$outfile = fopen($csv_path."ICD10CM.tabular.csv", "w");
	$header = ["code", "desc"];
	fputcsv($outfile, $header);
	convertJsonToCsv($json_path."ICD10CM.tabular.json", $outfile);
	fclose($outfile);
?>