<?php
return "SELECT
	deal.*,
	area.are_title,
	pro_addr1,pro_addr3,pro_postcode,
	a.line1                                                                                                                       AS line1,
	a.line2                                                                                                                       AS line2,
	a.line3                                                                                                                       AS line3,
	a.line4                                                                                                                       AS line4,
	a.line5                                                                                                                       AS line5,
	a.postcode                                                                                                                    AS postcode,
	pro_latitude,
	pro_longitude,
	branch.bra_id,
	branch.bra_title,
	branch.bra_tel,
	branch.bra_fax,
	T.pty_title                                                                                                                   AS ptype,
	ST.pty_title                                                                                                                  AS psubtype,

	GROUP_CONCAT(DISTINCT CONCAT(feature.fea_title) ORDER BY feature.fea_id ASC SEPARATOR '~')                                    AS features,
	GROUP_CONCAT(DISTINCT CONCAT(photos.med_file, '|', photos.med_title) ORDER BY photos.med_order ASC SEPARATOR '~')             AS photos,
	GROUP_CONCAT(DISTINCT CONCAT(floorplans.med_file, '|', floorplans.med_title) ORDER BY floorplans.med_order ASC SEPARATOR '~') AS floorplans,
	GROUP_CONCAT(DISTINCT CONCAT(epc.med_file, '|', epc.med_title) ORDER BY epc.med_order ASC SEPARATOR '~')                      AS epc
	FROM deal
		LEFT JOIN property ON deal.dea_prop = property.pro_id
		LEFT JOIN area ON property.pro_area = area.are_id
		LEFT JOIN branch ON deal.dea_branch = branch.bra_id
		LEFT JOIN ptype AS T
			ON deal.dea_ptype = T.pty_id
		LEFT JOIN ptype AS ST
			ON deal.dea_psubtype = ST.pty_id
		LEFT JOIN media AS photos
			ON deal.dea_id = photos.med_row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
		LEFT JOIN media AS floorplans
			ON deal.dea_id = floorplans.med_row AND floorplans.med_table = 'deal' AND floorplans.med_type = 'Floorplan'
		LEFT JOIN media AS epc
			ON deal.dea_id = epc.med_row AND epc.med_table = 'deal' AND epc.med_type = 'EPC'

		LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
		LEFT JOIN feature ON featureId = feature.fea_id
		LEFT JOIN address AS a
			ON a.id = property.addressId

	WHERE
		deal.dea_status IN ('Available', 'Under Offer', 'Under Offer with Other') AND deal.noPortalFeed <> 1 AND deal.underTheRadar <> 1 AND dea_type = 'sales'
	GROUP BY dea_id";
