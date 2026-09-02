<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_tweb() . 'core.sidebar.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<h4 class="fw-bold mb-4"><i class="fas fa-th-large me-2"></i>Secciones del Estacionamiento</h4>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-bold mb-0">Lista de Secciones</h6>
      <button class="btn btn-primary btn-sm" onclick="openModal()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">
        <i class="fas fa-plus me-1"></i>Nueva Sección
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-striped">
        <thead style="background: #f8f9fa;">
          <tr>
            <th>Nombre</th>
            <th>Capacidad</th>
            <th>Ocupados</th>
            <th>Disponibles</th>
            <th>Estatus</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbl-secciones">
          <tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Sección -->
<div class="modal fade" id="modalSeccion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
        <h5 class="modal-title fw-bold" id="modalTitle">Nueva Sección</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="sec-id">
        <div class="mb-3">
          <label class="form-label fw-bold">Nombre</label>
          <input type="text" class="form-control" id="sec-nombre" placeholder="Ej. Principal" style="border-radius: 8px;">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Capacidad</label>
          <input type="number" class="form-control" id="sec-capacidad" min="1" placeholder="50" style="border-radius: 8px;">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Descripción</label>
          <textarea class="form-control" id="sec-descripcion" rows="2" style="border-radius: 8px;"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Estatus</label>
          <select class="form-select" id="sec-estatus" style="border-radius: 8px;">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveSeccion()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
  var aData = [];
  var canDelete = <?php echo tieneRol('admin') ? 'true' : 'false'; ?>;

  function loadSecciones() {
    MsgServer(path.model + 'secciones.php', function(dat) {
      if (dat.result) {
        aData = dat.data;
        renderTable(dat.data);
      }
    }, { action: 'getall' });
  }

  function renderTable(data) {
    var html = '';
    if (data.length === 0) {
      html = '<tr><td colspan="6" class="text-center text-muted">No hay secciones registradas.</td></tr>';
    } else {
      data.forEach(function(r) {
        var disp = r.capacidad - r.ocupados;
        var badge = r.estatus === 'activo' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
        html += '<tr>';
        html += '<td class="fw-bold">' + r.nombre + '</td>';
        html += '<td>' + r.capacidad + '</td>';
        html += '<td>' + r.ocupados + '</td>';
        html += '<td>' + disp + '</td>';
        html += '<td>' + badge + '</td>';
        html += '<td class="text-center">';
        html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="editSeccion(' + r.id + ')"><i class="fas fa-edit"></i></button>';
        if (canDelete) {
          html += '<button class="btn btn-sm btn-outline-danger" onclick="deleteSeccion(' + r.id + ')"><i class="fas fa-trash"></i></button>';
        }
        html += '</td></tr>';
      });
    }
    $('#tbl-secciones').html(html);
  }

  function openModal(id) {
    $('#sec-id').val('');
    $('#sec-nombre').val('');
    $('#sec-capacidad').val('');
    $('#sec-descripcion').val('');
    $('#sec-estatus').val('activo');
    $('#modalTitle').text('Nueva Sección');
    new bootstrap.Modal(document.getElementById('modalSeccion')).show();
  }

  function editSeccion(id) {
    var item = aData.find(function(x){ return x.id == id; });
    if (!item) return;
    $('#sec-id').val(item.id);
    $('#sec-nombre').val(item.nombre);
    $('#sec-capacidad').val(item.capacidad);
    $('#sec-descripcion').val(item.descripcion);
    $('#sec-estatus').val(item.estatus);
    $('#modalTitle').text('Editar Sección');
    new bootstrap.Modal(document.getElementById('modalSeccion')).show();
  }

  function saveSeccion() {
    var aPar = {
      action: 'save',
      id: $('#sec-id').val(),
      nombre: $('#sec-nombre').val(),
      capacidad: $('#sec-capacidad').val(),
      descripcion: $('#sec-descripcion').val(),
      estatus: $('#sec-estatus').val()
    };
    MsgServer(path.model + 'secciones.php', function(dat) {
      if (dat.result) {
        MsgNotify("Sección guardada", "success");
        bootstrap.Modal.getInstance(document.getElementById('modalSeccion')).hide();
        loadSecciones();
      } else {
        MsgNotify(dat.message || "Error al guardar", "error");
      }
    }, aPar);
  }

  function deleteSeccion(id) {
    Swal.fire({ title: '¿Eliminar sección?', text: 'Esta acción no se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Eliminar' })
    .then(function(result) {
      if (result.isConfirmed) {
        MsgServer(path.model + 'secciones.php', function(dat) {
          if (dat.result) { MsgNotify("Sección eliminada", "success"); loadSecciones(); }
          else { MsgNotify(dat.message || "Error", "error"); }
        }, { action: 'delete', id: id });
      }
    });
  }

  $(function() { loadSecciones(); });
</script>

<?php include('view_footer.php'); ?>