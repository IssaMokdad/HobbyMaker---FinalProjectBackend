<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img height='200' width='238.5' style="object-fit:cover;" src="https://scontent.fbey12-1.fna.fbcdn.net/v/t1.0-9/103595750_10219860834063026_6522158140178520432_n.jpg?_nc_cat=111&_nc_sid=730e14&_nc_ohc=t0gsMMagiNYAX98BLKu&_nc_ht=scontent.fbey12-1.fna&oh=1fe4ecbcde9212a6493c62b3bf37f9da&oe=5F02A68D" class="logo" alt="HobbyMaker Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
