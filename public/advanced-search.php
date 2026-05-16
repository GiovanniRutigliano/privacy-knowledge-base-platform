<?php
require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

$results = null;
$searched = false;

// Process submitted query fields cleanly via discrete mapping arrays
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search_action'])) {
    $searched = true;
    $db = Database::getInstance();
    
    $sql = "SELECT * FROM patterns WHERE 1=1";
    $params = [];

    $filters = [
        'name'             => 'name',
        'strategies'       => 'strategies',
        'iso_phase'        => 'iso_phase',
        'collocazione_mvc' => 'collocazione_mvc',
        'gdpr_article'     => 'gdpr_article',
    ];

    foreach ($filters as $paramKey => $dbColumn) {
        $val = trim(filter_input(INPUT_GET, $paramKey, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        if ($val !== '') {
            $sql .= " AND {$dbColumn} LIKE :{$paramKey}";
            $params[":{$paramKey}"] = "%{$val}%";
        }
    }

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
    } catch (\Exception $e) {
        $results = [];
        error_log("Advanced filtering lookup failure: " . $e->getMessage());
    }
}

$page_title = "Advanced Pattern Lookup";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card p-4 shadow-sm mx-auto border-0 bg-light mb-5">
    <h3 class="text-center fw-bold mb-4"><i class="fas fa-sliders-h text-success me-2"></i> Advanced Filtering Engine</h3>
    <form action="/advanced-search.php" method="GET" class="row g-3">
        <input type="hidden" name="search_action" value="1">
        
        <div class="col-md-6">
            <label for="name" class="form-label fw-semibold small">Design Pattern Target</label>
            <select class="form-select shadow-none" name="name" id="name">
                <option value="">-- All Classifications --</option>
                <option value="Protection against Tracking">Protection against Tracking</option>
                <option value="Strip Invisible Metadata">Strip Invisible Metadata</option>
                <option value="Added-noise measurement obfuscation">Added-noise measurement obfuscation</option>
                <option value="Data Breach Notification Pattern">Data Breach Notification Pattern</option>
                <option value="Unusual Activities">Unusual Activities</option>
                <option value="Minimal Information Asymmetry">Minimal Information Asymmetry</option>
                <option value="Onion Routing">Onion Routing</option>
                <option value="Encryption with user-managed keys">Encryption with user-managed keys</option>
                <option value="Use of dummies">Use of dummies</option>
                <option value="Federated Privacy Impact Assessment">Federated Privacy Impact Assessment</option>
                <option value="Obligation Management">Obligation Management</option>
                <option value="Sticky Policies">Sticky Policies</option>
                <option value="Personal Data Store">Personal Data Store</option>
                <option value="User data confinement pattern">User data confinement pattern</option>
                <option value="Anonymous Reputation-based Blacklisting">Anonymous Reputation-based Blacklisting</option>
                <option value="Location Granularity">Location Granularity</option>
                <option value="Discouraging blanket strategies">Discouraging blanket strategies</option>
                <option value="Reciprocity">Reciprocity</option>
                <option value="Incentivized Participation">Incentivized Participation</option>
                <option value="Outsourcing [with consent]">Outsourcing [with consent]</option>
                <option value="Pseudonymous Messaging">Pseudonymous Messaging</option>
                <option value="Psuedonymous Identity">Psuedonymous Identity</option>
                <option value="Aggregation Gateway">Aggregation Gateway</option>
                <option value="Informed Secure Passwords">Informed Secure Passwords</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="strategies" class="form-label fw-semibold small">Privacy Engineering Strategy</label>
            <select class="form-select shadow-none" name="strategies" id="strategies">
                <option value="">-- All Strategies --</option>
                <option value="Minimize">Minimize</option>
                <option value="Hide">Hide</option>
                <option value="Enforce">Enforce</option>
                <option value="Separate">Separate</option>
                <option value="Abstract">Abstract</option>
                <option value="Control">Control</option>
                <option value="Inform">Inform</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="iso_phase" class="form-label fw-semibold small">ISO Design Lifecycle Phase</label>
            <select class="form-select shadow-none" name="iso_phase" id="iso_phase">
                <option value="">-- All Lifecycle Stages --</option>
                <option value="7.2">7.2-->Understanding context of use</option>
                <option value="7.3">7.3-->Specify requirements</option>
                <option value="7.4">7.4-->Producing design solutions</option>
                <option value="7.5">7.5-->Evaluating design</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="collocazione_mvc" class="form-label fw-semibold small">MVC Architecture Layer</label>
            <select class="form-select shadow-none" name="collocazione_mvc" id="collocazione_mvc">
                <option value="">-- All Layers --</option>
                <option value="Controller">Controller</option>
                <option value="Model">Model</option>
                <option value="View">View</option>
            </select>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-success px-5"><i class="fas fa-filter me-2"></i> Execute Search</button>
            <a href="/advanced-search.php" class="btn btn-outline-secondary ms-2">Reset</a>
        </div>
    </form>
</div>

<?php if ($searched): ?>
    <div class="card border-0 shadow-sm p-4">
        <h4 class="fw-bold mb-4">Filtering Results</h4>
        <?php if (empty($results)): ?>
            <div class="alert alert-warning text-center m-0">No pattern instances aligned with submitted parameter constraints.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle border">
                    <thead class="table-dark">
                        <tr>
                            <th>Classification</th>
                            <th>Strategy</th>
                            <th>ISO Target</th>
                            <th>MVC Mapping</th>
                            <th>Context Base</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td class="fw-bold">
                                    <a href="/pattern.php?id=<?= urlencode((string)$row['id']) ?>" class="text-primary text-decoration-none">
                                        <?= htmlspecialchars($row['name']) ?> <i class="fas fa-external-link-alt small ms-1"></i>
                                    </a>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['strategies']) ?></span></td>
                                <td class="small"><?= htmlspecialchars($row['iso_phase']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['collocazione_mvc']) ?></span></td>
                                <td class="small text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($row['context']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>