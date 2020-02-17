<div class="theme-info theme-{{ $theme->type }}">
  @if(!(isset($standalone) && $standalone))
    <h3> @if($theme->type === 'main') Main Theme @else Child Theme @endif </h3>
  @endif

  <div class="row">

    <div class="col-7 col-12-medium">

      <table class="table">
        <tbody>
          <tr>
            <th>Theme Name</th>
            <td>{!! $theme->name !!}</td>
          </tr>
          @if ($theme->version)
            <tr>
              <th>Version</th>
              <td>{{ $theme->version }}</td>
            </tr>
          @endif
          @if ($theme->uri)
            <tr>
              <th>URL</th>
              <td><a href="{{ $theme->uri }}" target="_blank">{{ $theme->uri }}</a></td>
            </tr>
          @endif
          <tr>
            <th>Description</th>
            <td>{!! $theme->description !!}</td>
          </tr>
          @if ($theme->author)
            <tr>
              <th>Author</th>
              <td>
                @if ($theme->author_uri)
                  <a href="{{ $theme->author_uri }}" target="_blank">{{ $theme->author }}</a>
                @else
                  {!! $theme->author !!}
                @endif
              </td>
            </tr>
          @endif
          @if ($theme->license)
            <tr>
              <th>License</th>
              <td>
                @if ($theme->license_uri)
                  <a href="{{ $theme->license_uri }}" target="_blank">{{ $theme->license }}</a>
                @else
                  {!! $theme->license !!}
                @endif
              </td>
            </tr>
          @endif
        </tbody>
      </table>

    </div><!-- col-6 -->

    <div class="col-5 col-12-medium">
      <span class="image fit">
        <img src="{{ $theme->screenshot_uri ? $theme->screenshot_uri : 'http://via.placeholder.com/350x350?text=No Screenshot' }}"
            alt="Theme Screenshot"/>
      </span>
    </div><!-- col-6 -->

  </div><!-- row -->
</div>
