<?php
	return array(
		'bekendmaking' 	=> array(
			'title' 		=> 'Bekendmaking',
			'url' 			=> 'https://zoek.officielebekendmakingen.nl/sru/Search',
			'default_attr' 	=> array(
				'version' 		=> '1.2',
				'operation' 	=> 'searchRetrieve'
			),
			'mapping' 		=> array(
				'startRecord' 		=> 'startRecord',
				'maximumRecords' 	=> 'maximumRecords',
				'query' 			=> 'query',
				'numberOfRecords' 	=> 'numberOfRecords',
				'records' 			=> 'records/record',
				'identifier' 		=> 'recordData/gzd/originalData/overheidop:meta/overheidop:owmskern/dcterms:identifier',
				'title' 			=> 'recordData/gzd/originalData/overheidop:meta/overheidop:owmskern/dcterms:title',
				'permalink' 		=> 'recordData/gzd/enrichedData/url',
				'meta' 				=> array(
					'subject' 			=> 'recordData/gzd/originalData/overheidop:meta/overheidop:owmsmantel/dcterms:subject',
					'organisationtype' 	=> 'recordData/gzd/originalData/overheidop:meta/overheidop:opmeta/overheid:organisationtype',
					'publicationname' 	=> 'recordData/gzd/originalData/overheidop:meta/overheidop:opmeta/overheid:publicationname',
				),
				'created_at' 		=> 'recordData/gzd/originalData/overheidop:meta/overheidop:owmsmantel/dcterms:date',
				'updated_at' 		=> 'recordData/gzd/originalData/overheidop:meta/overheidop:owmskern/dcterms:modified',
			)
		),
		'regelingen_verordeningen' => array(
			'title' 		=> 'Regelingen en verordeningen',
			'url' 			=> 'http://zoekdienst.overheid.nl/sru/Search',
			'default_attr' 	=> array(
				'version' 		=> '1.2',
				'operation' 	=> 'searchRetrieve',
				'x-connection' 	=> 'cvdr'
			),
			'mapping' 		=> array(
				'startRecord' 		=> 'startRecord',
				'maximumRecords' 	=> 'maximumRecords',
				'query' 			=> 'query',
				'numberOfRecords' 	=> 'numberOfRecords',
				'records' 			=> 'records/record',
				'identifier' 		=> 'recordData/gzd/originalData/overheidrg:meta/owmskern/dcterms:identifier',
				'title' 			=> 'recordData/gzd/originalData/overheidrg:meta/owmskern/dcterms:title',
				'permalink' 		=> 'recordData/gzd/enrichedData/publicatieurl_xhtml',
				'meta' 				=> array(
					'subject' 			=> 'recordData/gzd/originalData/overheidrg:meta/owmsmantel/dcterms:subject',
					'subject_alt' 		=> 'recordData/gzd/originalData/overheidrg:meta/cvdripm/overheidrg:onderwerp',
					'organisationtype' 	=> 'recordData/gzd/enrichedData/organisatietype',
					'publicationname' 	=> 'recordData/gzd/originalData/overheidrg:meta/owmsmantel/dcterms:isFormatOf',
					'betreft' 			=> 'recordData/gzd/originalData/overheidrg:meta/cvdripm/overheidrg:betreft',
					'kenmerk' 			=> 'recordData/gzd/originalData/overheidrg:meta/cvdripm/overheidrg:kenmerk',
				),
				'created_at' 		=> 'recordData/gzd/originalData/overheidrg:meta/owmsmantel/dcterms:issued',
				'updated_at' 		=> 'recordData/gzd/originalData/overheidrg:meta/owmskern/dcterms:modified',
			)
		)
	);