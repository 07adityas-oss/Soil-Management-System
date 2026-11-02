<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function h($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirectWithMessage(string $message, string $type='success'): never {
    $_SESSION['flash'] = ['message'=>$message, 'type'=>$type];
    header('Location: index.php');
    exit;
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$editRecord = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid request token. Refresh the page and try again.');
    }
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'soil_id', FILTER_VALIDATE_INT);
        if (!$id) redirectWithMessage('Invalid record ID.', 'error');
        $stmt = $pdo->prepare('DELETE FROM soil_records WHERE soil_id = :id');
        $stmt->execute([':id'=>$id]);
        redirectWithMessage($stmt->rowCount() ? 'Soil record deleted.' : 'Record not found.', $stmt->rowCount() ? 'success' : 'error');
    }

    if ($action === 'save') {
        $idRaw = trim((string)($_POST['soil_id'] ?? ''));
        $id = $idRaw === '' ? null : filter_var($idRaw, FILTER_VALIDATE_INT);
        $sample_code = trim((string)($_POST['sample_code'] ?? ''));
        $farmer_name = trim((string)($_POST['farmer_name'] ?? ''));
        $location = trim((string)($_POST['location'] ?? ''));
        $soil_type = trim((string)($_POST['soil_type'] ?? 'Other'));
        $phRaw = trim((string)($_POST['ph_value'] ?? ''));
        $nitrogenRaw = trim((string)($_POST['nitrogen_mg_kg'] ?? ''));
        $phosphorusRaw = trim((string)($_POST['phosphorus_mg_kg'] ?? ''));
        $potassiumRaw = trim((string)($_POST['potassium_mg_kg'] ?? ''));
        $moistureRaw = trim((string)($_POST['moisture_percent'] ?? ''));
        $recommended_crop = trim((string)($_POST['recommended_crop'] ?? ''));
        $sample_date = trim((string)($_POST['sample_date'] ?? ''));
        $notes = trim((string)($_POST['notes'] ?? ''));

        $allowedTypes = ['Alluvial','Black','Red','Laterite','Sandy','Clayey','Loamy','Other'];
        if ($idRaw !== '' && !$id) $errors[] = 'Invalid record ID.';
        if ($sample_code === '' || mb_strlen($sample_code) > 30) $errors[] = 'Sample code is required (maximum 30 characters).';
        if ($farmer_name === '' || mb_strlen($farmer_name) > 100) $errors[] = 'Farmer name is required (maximum 100 characters).';
        if ($location === '' || mb_strlen($location) > 150) $errors[] = 'Location is required (maximum 150 characters).';
        if (!in_array($soil_type, $allowedTypes, true)) $errors[] = 'Choose a valid soil type.';
        if ($phRaw === '' || !is_numeric($phRaw) || (float)$phRaw < 0 || (float)$phRaw > 14) $errors[] = 'pH must be a number between 0 and 14.';
        foreach (['Nitrogen'=>$nitrogenRaw,'Phosphorus'=>$phosphorusRaw,'Potassium'=>$potassiumRaw] as $label=>$value) {
            if ($value !== '' && (!is_numeric($value) || (float)$value < 0)) $errors[] = $label . ' must be blank or a non-negative number.';
        }
        if ($moistureRaw !== '' && (!is_numeric($moistureRaw) || (float)$moistureRaw < 0 || (float)$moistureRaw > 100)) $errors[] = 'Moisture must be between 0 and 100 percent.';
        if ($sample_date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $sample_date) || !checkdate((int)substr($sample_date,5,2), (int)substr($sample_date,8,2), (int)substr($sample_date,0,4))) $errors[] = 'Enter a valid sample date.';

        if (!$errors) {
            $params = [
                ':sample_code'=>$sample_code, ':farmer_name'=>$farmer_name, ':location'=>$location,
                ':soil_type'=>$soil_type, ':ph_value'=>(float)$phRaw,
                ':nitrogen_mg_kg'=>$nitrogenRaw === '' ? null : (float)$nitrogenRaw,
                ':phosphorus_mg_kg'=>$phosphorusRaw === '' ? null : (float)$phosphorusRaw,
                ':potassium_mg_kg'=>$potassiumRaw === '' ? null : (float)$potassiumRaw,
                ':moisture_percent'=>$moistureRaw === '' ? null : (float)$moistureRaw,
                ':recommended_crop'=>$recommended_crop === '' ? null : $recommended_crop,
                ':sample_date'=>$sample_date, ':notes'=>$notes === '' ? null : $notes
            ];
            try {
                if ($id) {
                    $params[':id'] = $id;
                    $sql = 'UPDATE soil_records SET sample_code=:sample_code, farmer_name=:farmer_name, location=:location,
                        soil_type=:soil_type, ph_value=:ph_value, nitrogen_mg_kg=:nitrogen_mg_kg,
                        phosphorus_mg_kg=:phosphorus_mg_kg, potassium_mg_kg=:potassium_mg_kg,
                        moisture_percent=:moisture_percent, recommended_crop=:recommended_crop,
                        sample_date=:sample_date, notes=:notes WHERE soil_id=:id';
                    $stmt = $pdo->prepare($sql); $stmt->execute($params);
                    redirectWithMessage($stmt->rowCount() ? 'Soil record updated.' : 'Update saved (or no values changed).');
                } else {
                    $sql = 'INSERT INTO soil_records (sample_code, farmer_name, location, soil_type, ph_value,
                        nitrogen_mg_kg, phosphorus_mg_kg, potassium_mg_kg, moisture_percent,
                        recommended_crop, sample_date, notes)
                        VALUES (:sample_code, :farmer_name, :location, :soil_type, :ph_value,
                        :nitrogen_mg_kg, :phosphorus_mg_kg, :potassium_mg_kg, :moisture_percent,
                        :recommended_crop, :sample_date, :notes)';
                    $stmt = $pdo->prepare($sql); $stmt->execute($params);
                    redirectWithMessage('New soil record created.');
                }
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') $errors[] = 'That sample code already exists. Use a unique sample code.';
                else $errors[] = 'The database could not save the record. Check the database setup and input values.';
            }
        }
        $editRecord = [
            'soil_id'=>$idRaw, 'sample_code'=>$sample_code, 'farmer_name'=>$farmer_name, 'location'=>$location,
            'soil_type'=>$soil_type, 'ph_value'=>$phRaw, 'nitrogen_mg_kg'=>$nitrogenRaw,
            'phosphorus_mg_kg'=>$phosphorusRaw, 'potassium_mg_kg'=>$potassiumRaw,
            'moisture_percent'=>$moistureRaw, 'recommended_crop'=>$recommended_crop,
            'sample_date'=>$sample_date, 'notes'=>$notes
        ];
    }
}

if (isset($_GET['edit']) && ctype_digit((string)$_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM soil_records WHERE soil_id = :id');
    $stmt->execute([':id'=>(int)$_GET['edit']]);
    $editRecord = $stmt->fetch() ?: null;
    if (!$editRecord) $flash = ['message'=>'Record not found.', 'type'=>'error'];
}
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM soil_records WHERE sample_code LIKE :q OR farmer_name LIKE :q OR location LIKE :q OR soil_type LIKE :q ORDER BY sample_date DESC, soil_id DESC');
    $stmt->execute([':q'=>'%'.$q.'%']);
    $records = $stmt->fetchAll();
} else {
    $records = $pdo->query('SELECT * FROM soil_records ORDER BY sample_date DESC, soil_id DESC')->fetchAll();
}
$count = (int)$pdo->query('SELECT COUNT(*) FROM soil_records')->fetchColumn();
$avgPh = $pdo->query('SELECT AVG(ph_value) FROM soil_records')->fetchColumn();
$r = $editRecord ?? [];
$editing = isset($r['soil_id']) && $r['soil_id'] !== '';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SoilCare | Soil Management System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <div class="brand-mark">S</div>
  <div><div class="brand">SoilCare</div><div class="brand-sub">SOIL MANAGEMENT SYSTEM</div></div>
  <span class="top-pill">DBMS PROJECT</span>
</header>
<main class="container">
  <section class="hero">
    <div><p class="eyebrow">AGRICULTURE · DATA · INSIGHTS</p><h1>Soil records, <em>well managed.</em></h1>
    <p class="hero-copy">Keep soil sample information organised in one simple database. Add, review, update and remove records with ease.</p></div>
    <div class="hero-art" aria-hidden="true"><div class="sun"></div><div class="hill hill-back"></div><div class="hill hill-front"></div><div class="sprout">✦</div></div>
  </section>
  <?php if ($flash): ?><div class="notice <?=h($flash['type'])?>"><?=h($flash['message'])?></div><?php endif; ?>
  <?php if ($errors): ?><div class="notice error"><strong>Please fix these issues:</strong><ul><?php foreach ($errors as $error): ?><li><?=h($error)?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <section class="stats">
    <article class="stat-card"><span class="stat-icon">▦</span><div><span class="stat-label">TOTAL RECORDS</span><strong><?=h($count)?></strong><small>Stored soil samples</small></div></article>
    <article class="stat-card"><span class="stat-icon">◉</span><div><span class="stat-label">AVERAGE pH</span><strong><?=$avgPh === null ? '—' : h(number_format((float)$avgPh, 1))?></strong><small>Across all records</small></div></article>
    <article class="stat-card"><span class="stat-icon">✓</span><div><span class="stat-label">DATABASE STATUS</span><strong>Connected</strong><small>MySQL connection active</small></div></article>
  </section>
  <section class="workspace">
    <aside class="panel form-panel">
      <div class="panel-heading"><div><p class="eyebrow"><?= $editing ? 'EDIT RECORD' : 'NEW ENTRY' ?></p><h2><?= $editing ? 'Update soil sample' : 'Add soil sample' ?></h2></div><span class="heading-icon">＋</span></div>
      <p class="muted">Fields marked * are required.</p>
      <form method="post" action="index.php" class="soil-form">
        <input type="hidden" name="csrf_token" value="<?=h($_SESSION['csrf_token'])?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="soil_id" value="<?=h($r['soil_id'] ?? '')?>">
        <label>Sample code *<input name="sample_code" maxlength="30" required placeholder="e.g. SM-2026-004" value="<?=h($r['sample_code'] ?? '')?>"></label>
        <label>Farmer / owner name *<input name="farmer_name" maxlength="100" required placeholder="Full name" value="<?=h($r['farmer_name'] ?? '')?>"></label>
        <label>Location *<input name="location" maxlength="150" required placeholder="Village, district, state" value="<?=h($r['location'] ?? '')?>"></label>
        <div class="field-grid">
          <label>Soil type *<select name="soil_type" required><?php foreach (['Alluvial','Black','Red','Laterite','Sandy','Clayey','Loamy','Other'] as $type): ?><option value="<?=h($type)?>" <?=($r['soil_type'] ?? 'Other')===$type?'selected':''?>><?=h($type)?></option><?php endforeach; ?></select></label>
          <label>pH value *<input type="number" name="ph_value" min="0" max="14" step="0.1" required placeholder="0–14" value="<?=h($r['ph_value'] ?? '')?>"></label>
        </div>
        <p class="section-label">NUTRIENT LEVELS <span>(mg/kg)</span></p>
        <div class="field-grid three">
          <label>Nitrogen<input type="number" name="nitrogen_mg_kg" min="0" step="0.01" value="<?=h($r['nitrogen_mg_kg'] ?? '')?>" placeholder="—"></label>
          <label>Phosphorus<input type="number" name="phosphorus_mg_kg" min="0" step="0.01" value="<?=h($r['phosphorus_mg_kg'] ?? '')?>" placeholder="—"></label>
          <label>Potassium<input type="number" name="potassium_mg_kg" min="0" step="0.01" value="<?=h($r['potassium_mg_kg'] ?? '')?>" placeholder="—"></label>
        </div>
        <div class="field-grid">
          <label>Moisture (%)<input type="number" name="moisture_percent" min="0" max="100" step="0.01" value="<?=h($r['moisture_percent'] ?? '')?>" placeholder="0–100"></label>
          <label>Sample date *<input type="date" name="sample_date" required value="<?=h($r['sample_date'] ?? date('Y-m-d'))?>"></label>
        </div>
        <label>Recommended crop<input name="recommended_crop" maxlength="120" placeholder="e.g. wheat, maize" value="<?=h($r['recommended_crop'] ?? '')?>"></label>
        <label>Notes<textarea name="notes" rows="3" placeholder="Additional observations…"><?=h($r['notes'] ?? '')?></textarea></label>
        <div class="form-actions"><button class="btn-primary" type="submit"><?= $editing ? 'Save changes' : '＋ Create record' ?></button><?php if ($editing): ?><a class="btn-secondary" href="index.php">Cancel edit</a><?php else: ?><button class="btn-secondary" type="reset">Clear form</button><?php endif; ?></div>
      </form>
    </aside>
    <section class="panel table-panel">
      <div class="table-top"><div><p class="eyebrow">YOUR DATABASE</p><h2>Soil sample records</h2><p class="muted">Browse and manage all saved entries.</p></div><span class="record-count"><?=count($records)?> shown</span></div>
      <form method="get" class="search-form"><span>⌕</span><input type="search" name="q" value="<?=h($q)?>" placeholder="Search sample, farmer, location…"><button type="submit">Search</button><?php if($q!==''):?><a href="index.php" class="clear-search">Clear</a><?php endif;?></form>
      <div class="table-wrap"><table><thead><tr><th>Sample / Farmer</th><th>Location</th><th>Soil</th><th>pH</th><th>Sample date</th><th>Actions</th></tr></thead><tbody>
      <?php if (!$records): ?><tr><td colspan="6" class="empty-state"><span>⌕</span><strong>No records found</strong><small>Try another search or add a new soil sample.</small></td></tr>
      <?php else: foreach ($records as $record): ?><tr>
        <td><strong><?=h($record['sample_code'])?></strong><small><?=h($record['farmer_name'])?></small></td>
        <td><?=h($record['location'])?></td><td><span class="soil-tag"><?=h($record['soil_type'])?></span></td>
        <td><span class="ph-value"><?=h(number_format((float)$record['ph_value'],1))?></span></td><td><?=h($record['sample_date'])?></td>
        <td><div class="actions"><a class="edit-link" href="index.php?edit=<?=h($record['soil_id'])?>">Edit</a><form method="post" onsubmit="return confirm('Delete this soil record permanently?');"><input type="hidden" name="csrf_token" value="<?=h($_SESSION['csrf_token'])?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="soil_id" value="<?=h($record['soil_id'])?>"><button class="delete-link" type="submit">Delete</button></form></div></td>
      </tr><?php endforeach; endif; ?>
      </tbody></table></div>
      <p class="table-foot">Showing <?=count($records)?> record(s)<?= $q!=='' ? ' matching “'.h($q).'”' : '' ?> · Data stored in MySQL</p>
    </section>
  </section>
  <footer><span>SoilCare <span class="footer-dot">●</span> DBMS Mini Project</span><span>Designed for learning and demonstration</span></footer>
</main>
</body>
</html>
