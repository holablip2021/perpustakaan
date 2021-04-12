@include(Session::get('main'))
<hr class="featurette-divider border-0 bg-white">
<section id="aboutme">
    {{ Session::get('status') }}  
<div class="col-12 col-md-7 order-md-2">
    <table class="table table-bordered">
    <thead>
    <tr>
    <th>Id</th>
    <th>Stok Tersedia</th>
    <th>Aksi</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $results['buku_id'] }}</td>
        <td>{{ $results['stok'] }}</td>
        <td><a href="{{ url('/order/' . $results['buku_id'] ) }}" class="btn btn-primary" >Proses Pinjam</a></td>
    </tr>    
    </tbody>
    </table>
</div>        
</section>
</body>
</html>

<div class="modal" tabindex="-1" id="form_ask">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Hapus Data</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
      </div>
      <div class="modal-body">
        <div id="delete_id"></div>
        <form action="{{ url('/hapus/' ) }}" method = "post">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="hidden" id="field_id" name="field_id" class="form-control" required>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Hapus</button>
      </div>
    </div>
  </div>
</div>



    <script type="text/javascript">
        function form_ask(id) {
            $('#delete_id').html("<p>Apakah Id nomor "+ id + " akan dihapus ?</p>")
            $('#field_id').val(id)
            $("#form_ask").modal()
        }
    </script>
