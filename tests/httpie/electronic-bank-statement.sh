http POST http://twinfield.local/wp-json/pronamic-twinfield/v1/bank-statements \
	authorization=47 \
	office_code=11024 \
	code=MOLLIE_2 \
	date=2022-11-30 \
	statement_number=20221130 \
	'transactions[0][contra_iban]=NL13TEST0123456789' \
	'transactions[0][type]=NMSC' \
	'transactions[0][reference]=tr_test' \
	'transactions[0][debit_credit]=debit' \
	'transactions[0][value]=10.25' \
	'transactions[0][description]=Test description' \
