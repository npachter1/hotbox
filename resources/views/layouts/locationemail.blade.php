<?php $alert = (isset($alert) ? $alert : false); ?>
<?php $relay = (isset($relay) ? $relay : 'tickler'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} Tickler</title>
</head>
<body marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<style>
	/* Client-specific Styles */
	#outlook a {
	  padding: 0; }
	
	/* Force Outlook to provide a "view in browser" menu link. */
	body {
	  width: 100% !important;
	  -webkit-text-size-adjust: 100%;
	  -ms-text-size-adjust: 100%;
	  margin: 0;
	  padding: 0; }
	
	/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
	.ExternalClass {
	  width: 100%; }
	
	/* Force Hotmail to display emails at full width */
	.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
	  line-height: 100%; }
	
	/* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
	#backgroundTable {
	  margin: 0;
	  padding: 0;
	  width: 100% !important;
	  line-height: 100% !important; }
	
	img {
	  outline: none;
	  text-decoration: none;
	  border: none;
	  -ms-interpolation-mode: bicubic; }
	
	a img {
	  border: none; }
	
	.image_fix {
	  display: block; }
	
	p {
	  margin: 0px 0px !important; }
	
	table td {
	  border-collapse: collapse; }
	
	table {
	  border-collapse: collapse;
	  mso-table-lspace: 0pt;
	  mso-table-rspace: 0pt; }
	
	/*a {color: #e95353;text-decoration: none;text-decoration:none!important;}*/
	/*STYLES*/
	table[class=full] {
	  width: 100%;
	  clear: both; }
	
	/*################################################*/
	/*IPAD STYLES*/
	/*################################################*/
	@media only screen and (max-width: 640px) {
	  a[href^="tel"], a[href^="sms"] {
	    text-decoration: none;
	    color: #ffffff;
	    /* or whatever your want */
	    pointer-events: none;
	    cursor: default; }
	
	  .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
	    text-decoration: default;
	    color: #ffffff !important;
	    pointer-events: auto;
	    cursor: default; }
	
	  table[class=devicewidth] {
	    width: 440px !important;
	    text-align: center !important; }
	
	  table[class=devicewidthinner] {
	    width: 420px !important;
	    text-align: center !important; }
	
	  table[class="sthide"] {
	    display: none !important; }
	
	  img[class="bigimage"] {
	    width: 420px !important;
	    height: 219px !important; }
	
	  img[class="col2img"] {
	    width: 420px !important;
	    height: 258px !important; }
	
	  img[class="image-banner"] {
	    width: 440px !important;
	    height: 106px !important; }
	
	  td[class="menu"] {
	    text-align: center !important;
	    padding: 0 0 10px 0 !important; }
	
	  td[class="logo"] {
	    padding: 10px 0 5px 0 !important;
	    margin: 0 auto !important; }
	
	  img[class="logo"] {
	    padding: 0 !important;
	    margin: 0 auto !important; } }
	/*##############################################*/
	/*IPHONE STYLES*/
	/*##############################################*/
	@media only screen and (max-width: 480px) {
	  a[href^="tel"], a[href^="sms"] {
	    text-decoration: none;
	    color: #ffffff;
	    /* or whatever your want */
	    pointer-events: none;
	    cursor: default; }
	
	  .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
	    text-decoration: default;
	    color: #ffffff !important;
	    pointer-events: auto;
	    cursor: default; }
	
	  table[class=devicewidth] {
	    width: 280px !important;
	    text-align: center !important; }
	
	  table[class=devicewidthinner] {
	    width: 260px !important;
	    text-align: center !important; }
	
	  table[class="sthide"] {
	    display: none !important; }
	
	  img[class="bigimage"] {
	    width: 260px !important;
	    height: 136px !important; }
	
	  img[class="col2img"] {
	    width: 260px !important;
	    height: 160px !important; }
	
	  img[class="image-banner"] {
	    width: 280px !important;
	    height: 68px !important; } }
	/*##############################################*/
	/*CONTENT STYLE*/
	/*##############################################*/
	body {
	  background: #f6f4f5; }
	
	.title {
	  font-family: Arial, sans-serif;
	  font-size: 18px;
	  color: #333333;
	  text-align: left;
	  /*line-height: 100% !important;*/
	}
	
	.title.center {
	  text-align: center; }
	
	.paragraph {
	  font-family: Arial, sans-serif;
	  font-size: 14px;
	  color: #666666;
	  text-align: left;
	  /*line-height: 100% !important;*/
	}
	
	.paragraph.alt {
	  font-size: 14px;
	  color: #95a5a6;
	  text-align: center;
	  /*line-height: 135% !important;*/
	}
	</style>

	<div class="block">
		<!-- start of header -->
		<table width="100%" bgcolor="#FFF" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="header">
			<tbody>
			<tr>
				<td>
					<table width="600" bgcolor="{!! ($alert==true ? '#dd4b39' : '#9ec1d5') !!}" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" hlitebg="edit" shadow="edit">
						<tbody>
						<tr>
							<td>
								<!-- header -->
								<table width="600" cellpadding="0" cellspacing="0" border="0" align="left" class="devicewidth">
									<tbody>
									<tr>
										<td valign="middle" width="600" align="center" style="padding: 6px 0 6px 20px;" class="">
												<center><h2><a href="{!! url('admin/dashboard') !!}" style="color:#FFF !important; text-decoration: none !important; line-height:1.2 !important">
												   A Message From {!! (isset($title) ? $title : config('app.name')) !!}
												 </a></h2></center>
										</td>
									</tr>
									</tbody>
								</table>
								<!-- end header -->
							</td>
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
			</tbody>
		</table>
		<!-- end of header -->
	</div>
	
	@yield('content')
	
	<div class="block">
		<!-- start of header -->
		<table width="100%" bgcolor="#fff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="header">
			<tbody>
			<tr>
				<td>
					<table width="600" bgcolor="{!! ($alert==true ? '#dd4b39' : '#9ec1d5') !!}" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" hlitebg="edit" shadow="edit">
						<tbody>
						<tr>
							<td>
								<!-- menu -->
								<table width="600" cellpadding="0" cellspacing="0" border="0" align="right" class="devicewidth">
									<tbody>
									<tr>
										<td width="600" align="center" valign="middle" style="font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #FFF; line-height: 1.2 !important; padding: 10px 0;" align="right" class="menu" st-content="menu">
										    <a href="{{ $saleOrder->location->address->website }}" style="color:#F1F1F1">{{ $saleOrder->location->address->website }}</a>
										</td>
									</tr>
									</tbody>
								</table>
								<!-- End of Menu -->
							</td>
						</tr>
						</tbody>
					</table>
				</td>
			</tr>
			</tbody>
		</table>
		<!-- end of header -->
	</div>
	
</body>
</html>