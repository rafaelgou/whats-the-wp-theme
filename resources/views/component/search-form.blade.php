<form class="form-url" method="POST" action="/" id="search-form">
  @csrf
  <div class="text-center">
    @if (isset($title) && $title) <h1 class="h3 mb-3 font-weight-normal">Altitude</h1> @endif
    @if (isset($newSearch) && $newSearch) <h2 class="h3 mb-3 font-weight-normal">New Search</h2> @endif
    <label for="inputUrl" class="sr-only">Site URL</label>
    <input type="url" id="inputUrl" name="uri" class="form-control" placeholder="http://example.com" @if (isset($uri)) value="{{ $uri }}" @endif required>
    <button id="submit-btn" class="btn btn-lg btn-primary btn-block" type="submit">Discover theme!</button>
  </div>
</form>
<script>
$(function(){
  $('#search-form').submit(function(event) {
    event.preventDefault();
    $('#submit-btn').attr('disabled', true).html('<i class="fas fa-asterisk fa-spin"></i> discovering...')
    this.submit();
  });
});
</script>
