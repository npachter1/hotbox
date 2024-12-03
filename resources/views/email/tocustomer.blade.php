@extends('layouts/locationemail')

@section('content')

<div class="block">
	<!-- Full + text -->
	<table width="100%" bgcolor="#fff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fullimage">
	  <tbody>
  		  <tr>
          <td>
            <table bgcolor="#ffffff" width="600" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" modulebg="edit">
					 <tbody>
					    <tr>
						    <td width="100%" height="20"></td>
					    </tr>
					    <tr>
						    <td>
							    <table width="560" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidthinner">
								    <tbody>
								  
								      <tr>
								        <td width="100%" style="line-height:1.2 !important">
								          <h2>Hi {!! (isset($saleOrder->customer->first_name)) ? $saleOrder->customer->first_name : $saleOrder->customer->alias !!},</h2>
								        </td>
								      </tr>
								      
								      <tr>
								        <td width="96%" style="margin-left:4%; line-height:1.2 !important">
							              <p>&nbsp;</p>
							                 {!! preg_replace('/ -- /','<br><br>',nl2br($mes)) !!}
                                          <p>&nbsp;</p>
								        </td>
								      </tr>
 
								      <tr>
								        <td width="100%" style="line-height:1 !important; margin-top:18px;">
                                          <p>&nbsp;</p>
                                          <p>&nbsp;</p>
                                          	<p><center>{{$saleOrder->location->name}} | License Number: {{$saleOrder->location->licensenum}} | {{$saleOrder->location->address->address1}} - {{$saleOrder->location->address->city}}, {{$saleOrder->location->address->region}} {{$saleOrder->location->address->zip}}</center></p>
                                          <p>&nbsp;</p>
								        </td>
								      </tr>
								</tbody>
                          </table>
                        </td>
                      </tr>
                </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</div>

@stop