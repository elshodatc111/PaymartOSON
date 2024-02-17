<form action="{{ route('paymart') }}" method="post">
    @csrf 
    <input type="hidden" name="UserID" value="199701013">
    <input type="hidden" name="oson" value="true">
    <button type="submit">Send</button>
</form>