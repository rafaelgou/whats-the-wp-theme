<form method="POST" action="/search" id="search-form">
  @csrf
    <div class="row gtr-uniform">
      <div class="col-6 col-12-xsmall">
        <input type="url" id="inputUrl" name="uri" placeholder="http://example.com" @if (isset($uri)) value="{{ $uri }}" @endif required>
      </div>
      <div class="col-6 col-12-xsmall">
        <button id="submit-btn" class="primary" type="submit">Discover theme!</button>
      </div>
  </div>
</form>
