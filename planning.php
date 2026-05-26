<?php
$mdp        = "samir2026";   // ← change ce mot de passe
$dispo_file = __DIR__ . "/dispo.txt";
$labels     = ["❌ Indispo", "✅ Dispo", "🟡 Matin", "🟠 Après-midi"];
 
$msg = "";
 
// Enregistrement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['mdp'] ?? '') !== $mdp) {
        $msg = "❌ Mot de passe incorrect";
    } else {
        $vals = [];
        foreach (['lun','mar','mer','jeu','ven'] as $j) {
            $v = intval($_POST[$j] ?? 1);
            $vals[] = max(0, min(3, $v));
        }
        file_put_contents($dispo_file, implode(',', $vals));
        $msg = "✅ Planning enregistré !";
    }
}
 
// Lecture fichier actuel
$current = [1,1,1,1,1];
if (file_exists($dispo_file)) {
    $parts = explode(',', trim(file_get_contents($dispo_file)));
    if (count($parts) === 5) $current = array_map('intval', $parts);
}
 
$jours = ['lun'=>'Lundi','mar'=>'Mardi','mer'=>'Mercredi','jeu'=>'Jeudi','ven'=>'Vendredi'];
 
// Dates de la semaine
$monday = new DateTime('monday this week');
$dates  = [];
for ($i = 0; $i < 5; $i++) {
    $d = clone $monday;
    $d->modify("+$i days");
    $dates[] = $d->format('d/m');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Planning Samir</title>
<style>
  body { font-family: Segoe UI, sans-serif; background:#0f0f13; color:#eee; display:flex; justify-content:center; padding:40px 16px; }
  .card { background:#1a1d26; border-radius:14px; padding:32px; width:100%; max-width:420px; box-shadow:0 8px 32px #0008; }
  h1 { font-size:20px; margin:0 0 24px; color:#1ec878; }
  .jour { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #2a2d3a; }
  .jour:last-of-type { border:none; }
  .nom { font-weight:600; font-size:15px; }
  .date { font-size:12px; color:#666; margin-left:6px; }
  select { background:#0f0f13; color:#fff; border:2px solid #2a2d3a; border-radius:8px; padding:6px 10px; font-size:14px; cursor:pointer; }
  input[type=password] { width:100%; padding:10px; background:#0f0f13; border:2px solid #2a2d3a; border-radius:8px; color:#fff; font-size:14px; box-sizing:border-box; margin-bottom:16px; }
  button { width:100%; padding:14px; background:#1ec878; color:#000; border:none; border-radius:10px; font-size:16px; font-weight:700; cursor:pointer; margin-top:20px; }
  button:hover { background:#17a86a; }
  .msg { text-align:center; padding:10px; border-radius:8px; margin-bottom:16px; background:#1a2a1a; color:#1ec878; }
  .msg.err { background:#2a1a1a; color:#e74c3c; }
</style>
</head>
<body>
<div class="card">
  <h1>📅 Planning Samir</h1>
  <p style="color:#666;font-size:13px;margin-top:-16px;margin-bottom:20px">
    Semaine du <?= $monday->format('d/m/Y') ?>
  </p>
 
  <?php if ($msg): ?>
    <div class="msg <?= str_contains($msg,'❌')?'err':'' ?>"><?= $msg ?></div>
  <?php endif; ?>
 
  <form method="POST">
    <?php $i=0; foreach ($jours as $key => $nom): ?>
    <div class="jour">
      <span>
        <span class="nom"><?= $nom ?></span>
        <span class="date"><?= $dates[$i] ?></span>
      </span>
      <select name="<?= $key ?>">
        <?php foreach ($labels as $v => $l): ?>
          <option value="<?= $v ?>" <?= $current[$i]==$v?'selected':'' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php $i++; endforeach; ?>
 
    <div style="margin-top:20px">
      <input type="password" name="mdp" placeholder="🔒 Mot de passe" required>
    </div>
    <button type="submit">💾 Enregistrer</button>
  </form>
</div>
</body>
</html>
