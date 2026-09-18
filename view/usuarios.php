<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_root() . 'helpers.php' );

checkAcceso(['admin']);

include('view_header.php');
?>

<h4 class="fw-bold mb-3"><i class="fas fa-users-cog me-2"></i>Usuarios</h4>

<div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
  <div class="card-body py-2 px-2">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="fw-bold mb-0" style="font-size:13px;">Lista de Usuarios</h6>
      <button class="btn btn-primary btn-sm" onclick="openModal()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none; font-size:12px;">
        <i class="fas fa-plus me-1"></i>Nuevo
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th class="d-none d-md-table-cell">Creado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbl-usuarios">
          <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
        <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="usr-id">
        <div class="mb-3">
          <label class="form-label fw-bold">Nombre Completo</label>
          <input type="text" class="form-control" id="usr-nombre" placeholder="Ej. Juan Pérez" style="border-radius: 8px;">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Usuario</label>
          <input type="text" class="form-control" id="usr-usuario" placeholder="Ej. juan" style="border-radius: 8px;">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold" id="lbl-clave">Contraseña</label>
          <input type="password" class="form-control" id="usr-clave" placeholder="Mínimo 6 caracteres" style="border-radius: 8px;">
          <small class="text-muted" id="clave-hint">Dejar vacío para no cambiar</small>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Rol</label>
          <select class="form-select" id="usr-rol" style="border-radius: 8px;">
            <option value="operador">Operador</option>
            <option value="cajero">Cajero</option>
            <option value="admin">Administrador</option>
          </select>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveUsuario()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
  var aData = [];

  function loadUsuarios() {
    MsgServer(path.model + 'usuarios.php', function(dat) {
      if (dat.result) {
        aData = dat.data;
        renderTable(dat.data);
      }
    }, { action: 'getall' });
  }

  function renderTable(data) {
    var html = '';
    if (data.length === 0) {
      html = '<tr><td colspan="5" class="text-center text-muted">No hay usuarios.</td></tr>';
    } else {
      data.forEach(function(r) {
        var badge = r.rol === 'admin' ? 'bg-danger' : r.rol === 'cajero' ? 'bg-warning text-dark' : 'bg-primary';
        html += '<tr>';
        html += '<td class="fw-bold" style="font-size:12px;">' + r.username + '</td>';
        html += '<td>' + r.user + '</td>';
        html += '<td><span class="badge ' + badge + '" style="font-size:10px;">' + r.rol.toUpperCase() + '</span></td>';
        html += '<td class="d-none d-md-table-cell"><small>' + r.created_at + '</small></td>';
        html += '<td class="text-center">';
        html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="editUsuario(' + r.id + ')"><i class="fas fa-edit"></i></button>';
        html += '<button class="btn btn-sm btn-outline-danger" onclick="deleteUsuario(' + r.id + ')"><i class="fas fa-trash"></i></button>';
        html += '</td></tr>';
      });
    }
    $('#tbl-usuarios').html(html);
  }

  function openModal() {
    $('#usr-id').val('');
    $('#usr-nombre').val('');
    $('#usr-usuario').val('');
    $('#usr-clave').val('');
    $('#usr-rol').val('operador');
    $('#modalTitle').text('Nuevo Usuario');
    $('#lbl-clave').text('Contraseña');
    $('#clave-hint').text('').addClass('d-none');
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
  }

  function editUsuario(id) {
    var item = aData.find(function(x){ return x.id == id; });
    if (!item) return;
    $('#usr-id').val(item.id);
    $('#usr-nombre').val(item.username);
    $('#usr-usuario').val(item.user);
    $('#usr-clave').val('');
    $('#usr-rol').val(item.rol);
    $('#modalTitle').text('Editar Usuario');
    $('#lbl-clave').text('Nueva Contraseña (opcional)');
    $('#clave-hint').removeClass('d-none');
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
  }

  function saveUsuario() {
    var aPar = {
      action: 'save',
      id: $('#usr-id').val(),
      username: $('#usr-nombre').val(),
      user: $('#usr-usuario').val(),
      clave: $('#usr-clave').val(),
      rol: $('#usr-rol').val()
    };
    MsgServer(path.model + 'usuarios.php', function(dat) {
      if (dat.result) {
        MsgNotify("Usuario guardado", "success");
        bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
        loadUsuarios();
      } else {
        MsgNotify(dat.message || "Error al guardar", "error");
      }
    }, aPar);
  }

  function deleteUsuario(id) {
    Swal.fire({ title: '¿Eliminar usuario?', text: 'Esta acción no se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Eliminar' })
    .then(function(result) {
      if (result.isConfirmed) {
        MsgServer(path.model + 'usuarios.php', function(dat) {
          if (dat.result) { MsgNotify("Usuario eliminado", "success"); loadUsuarios(); }
          else { MsgNotify(dat.message || "Error", "error"); }
        }, { action: 'delete', id: id });
      }
    });
  }

  $(function() { loadUsuarios(); });
</script>

<?php include('view_footer.php'); ?>