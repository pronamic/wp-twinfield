<script src="https://unpkg.com/codemirror@5.38.0/lib/codemirror.js"></script>
<script src="https://unpkg.com/codemirror@5.38.0/mode/xml/xml.js"></script>

<link rel="stylesheet" type="text/css" href="https://unpkg.com/codemirror@5.38.0/lib/codemirror.css" />

<style type="text/css">
	.CodeMirror {
		height: 100%;
	}
</style>

<script type="text/javascript">
	const textareas = document.querySelectorAll( '.pronamic-twinfield-xml-textarea' );

	textareas.forEach( ( textarea ) => {
		editor = CodeMirror.fromTextArea(textarea, {
			lineNumbers: true,
			mode: 'application/xml'
		});
	} );
</script>
