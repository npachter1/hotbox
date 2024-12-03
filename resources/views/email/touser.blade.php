@extends('layouts/email')

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
								          <h2>Hi {{$user->name}},</h2>
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
								        <td width="100%">
								          <table height="36" align="center" valign="middle" border="0" cellpadding="0" cellspacing="0" class="tablet-button" st-button="edit">
                	           <tbody>
                	              <tr>
                		              <td width="auto" align="center" valign="middle" height="36" style=" background-color:{!! (isset($alert) ? ($alert==true ? '#dd4b39' : '#dd9239') : '#dd9239') !!}; border-top-left-radius:4px; border-bottom-left-radius:4px;border-top-right-radius:4px; border-bottom-right-radius:4px; background-clip: padding-box;font-size:13px; font-family:Helvetica, arial, sans-serif; text-align:center;  color:#ffffff; font-weight: 300;">
                	                  <span style="color: #ffffff; font-weight: 300;">
                	                    <a style="color: #ffffff; text-align:center;text-decoration: none;display:block;padding-left:25px; padding-right:25px;padding-bottom:6px;padding-top:6px;background-clip: padding-box;height:36px;line-height: 36px" href="{!! $cta_url !!}">{{ $cta_title }} &raquo;</a>
                	                  </span>
                		              </td>
                	              </tr>
                	           </tbody>
                           </table>
								        </td>
								      </tr>
								      
								      <tr>
								        <td width="100%" style="line-height:1 !important; margin-top:18px;">
                                          <p>&nbsp;</p>
                                          <p>&nbsp;</p>
                                          	<p><center>Hotbox ERP Inc. | 2535 17TH STREET SUITE B - DENVER, CO 80211</center></p>
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