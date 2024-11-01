@page { 
	margin: 0;
	/* The @page size attribute is unsupported by many browsers, but at lease we can try! http://stackoverflow.com/q/138422/1446634*/
	size: <?php echo $this->paper_size; ?>;
} 

html, body {
	margin: 0;
	padding: 0;
	border: none;
	height: 100%;
	font-size: <?php echo isset($this->settings['font_size'])?$this->settings['font_size'] . 'pt':'12px'; ?>;
	font-family: sans-serif;
}

table {
	border-collapse: collapse;
	page-break-after: always; 
}

.label-wrapper {
	/* adjust these values to a value smaller tan the actual label height/width if you're having trouble fitting the labels on a page */
	max-height: <?php echo $this->label_height; ?>mm;
	max-width: <?php echo $this->label_width; ?>mm;
	overflow: hidden;
}

td.label {
	vertical-align: middle;
	padding: 0;
	/*border: 1px solid black;*/
	/* ^^^ can be used for testing/debugging*/
}

.address-block {
	width: <?php echo isset($this->settings['block_width'])?$this->settings['block_width']:'5cm'; ?>;
	margin: auto;
	text-align: left;
	clear:both;
}

.qr_show {
    float: left;
}
.addrress_show
{
	float: left;
	margin-top:5px;
	}
.clearb{
	clear:both;
	
}
