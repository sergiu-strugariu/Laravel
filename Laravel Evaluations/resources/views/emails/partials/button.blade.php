<table class="wrapper">
    <tbody>
    <tr>
        <th class="small-12 large-4 columns first">
            <table class="first button facebook expand">
                <tbody>
                <tr>
                    <td>
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <center data-parsed=""><a
                                                class="float-center"
                                                @if(isset($style))
                                                    style="{{$style}}"
                                                @else
                                                    style="background: red; color: white; width: 180px;"
                                                @endif
                                                align="center"
                                                href="{{ $href }}">{{ $text }}</a>
                                    </center>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="expander"></td>
                </tr>
                </tbody>
            </table>
        </th>
        <th class="small-12 large-8 columns last">
            <table>
                <tbody>
                <tr>
                    <th>
                        &nbsp;</th>
                </tr>
                </tbody>
            </table>
        </th>
    </tr>
    </tbody>
</table>