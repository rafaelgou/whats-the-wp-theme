<h4 class="text-center">Top Searched Themes</h4>
<ul class="list-group">
  @foreach ($topSearched as $theme)
    <li class="list-group-item"><a href="/{{ strtolower($theme->name) }}">{{ $theme->name }}</a></li>
  @endforeach
</ul>
