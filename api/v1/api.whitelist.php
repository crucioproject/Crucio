<?php

$app->group('/whitelist', function() {

	$this->get('', function($request, $response, $args) {
		$mysql = init();

        $limit = intval($request->getQueryParam('limit', 10000));
        $query = strlen($request->getQueryParam('query')) > 0 ? "%".$request->getQueryParam('query')."%" : null;

		$stmt = $mysql->prepare(
		    "SELECT w.*, IF(u.user_id IS NOT NULL, 1, 0) as 'used'
            FROM whitelist w
            LEFT JOIN users u ON u.email = w.mail_address
            WHERE ( w.mail_address LIKE IFNULL(:query, w.mail_address) )
            LIMIT :limit"
		);
		$stmt->bindValue(':query', $query);
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $data['whitelist'] = getAll($stmt);
		return createResponse($response, $data);
	});

	$this->post('', function($request, $response, $args) {
    	$mysql = init();
		$body = $request->getParsedBody();

		$stmt = $mysql->prepare(
		    "INSERT
		    INTO whitelist ( mail_address )
		    VALUES (:mail_address)"
		);
		$stmt->bindValue(':mail_address', str_replace('(@)', '@', sanitize($body['email'])));

		$data['status'] = $stmt->execute();
		return createResponse($response, $data);
	});

	$this->delete('/{mail_address}', function($request, $response, $args) {
		$mysql = init();

		$stmt = $mysql->prepare(
		    "DELETE
		    FROM whitelist
		    WHERE mail_address = :mail_address"
		);
		$stmt->bindValue(':mail_address', $args['mail_address']);

        $data['status'] = $stmt->execute();
		return createResponse($response, $data);
	});
});

?>
