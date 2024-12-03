@extends('layouts/customeremail')

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
                                            <h2>Hi {!! (isset($customer->first_name)) ? $customer->first_name : $customer->alias !!},</h2>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td width="100%" style="line-height:1 !important; margin-top:18px;">
                                            <p>&nbsp;</p>
                                            <p>&nbsp;</p>
                                            <p><center>Thank you for signing up. You username is {{$customer->address->email}}</center></p>
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
