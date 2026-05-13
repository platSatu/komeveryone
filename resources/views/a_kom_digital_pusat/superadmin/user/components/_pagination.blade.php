 {{-- <div class="d-flex justify-content-center">
     {{ $data->withQueryString()->links() }}
 </div> --}}
 <div class="pagination justify-content-center mt-4">
     {{ $data->withQueryString()->links('pagination::bootstrap-4') }}
 </div>
