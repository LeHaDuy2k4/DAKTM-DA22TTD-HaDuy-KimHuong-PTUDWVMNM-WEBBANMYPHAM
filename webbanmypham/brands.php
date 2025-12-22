<?php
// BẮT BUỘC: Đặt ở dòng 1, không có khoảng trắng phía trên
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require("header.php"); 
require_once("config.php"); 

// --- LOGIC TRUY VẤN THƯƠNG HIỆU ĐỘNG TỪ DATABASE (GIỮ NGUYÊN) ---

$brands = []; 

// Kiểm tra kết nối database và thực hiện truy vấn
if (isset($conn) && $conn->connect_error === null) {
  // GIẢ ĐỊNH: Bảng 'thuonghieu' có thêm các cột 'quocGia' và 'moTa'
  $sql_brands = "SELECT maTH, tenTH, quocGia, moTa FROM thuonghieu ORDER BY tenTH ASC";
  $result_brands = $conn->query($sql_brands);

  if ($result_brands && $result_brands->num_rows > 0) {
    while($row = $result_brands->fetch_assoc()) {
      $brands[] = $row;
    }
  }
} else {
  // Xử lý lỗi hoặc hiển thị mảng rỗng nếu không có kết nối
  $brands = [];
}

// --- KẾT THÚC LOGIC TRUY VẤN THƯƠNG HIỆU ĐỘNG ---

// Hàm nhóm thương hiệu theo chữ cái đầu (GIỮ NGUYÊN)
function groupBrands($brands) {
  $grouped = [];
  $letters = array_merge(['0-9'], range('A', 'Z')); 

  foreach ($letters as $letter) {
    $grouped[$letter] = [];
  }
  
  foreach ($brands as $brand) {
    $first_char = strtoupper(substr(trim($brand['tenTH']), 0, 1));
    
    if (is_numeric($first_char)) {
      $grouped['0-9'][] = $brand;
    } elseif (array_key_exists($first_char, $grouped)) {
      $grouped[$first_char][] = $brand;
    }
  }
  // Sắp xếp các thương hiệu trong mỗi nhóm theo tên
  foreach ($grouped as $key => $brand_list) {
    usort($grouped[$key], function($a, $b) {
      return strcmp($a['tenTH'], $b['tenTH']);
    });
  }
  return $grouped;
}

$grouped_brands = groupBrands($brands);

$available_letters = [];
foreach ($grouped_brands as $letter => $brands_list) {
  if (!empty($brands_list)) {
    $available_letters[] = $letter;
  }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
/* ... (PHẦN CSS GIỮ NGUYÊN) ... */
:root {
--cosmetics-accent-color: #E91E63; 
--cosmetics-light-bg: #fff8fb; 
--cosmetics-text-dark: #333;
--cosmetics-text-light: #666;
}

body {
margin: 0;
font-family: 'Times New Roman', Times, serif;
background: #f7f7f7; 
}

.brands-page-container {
max-width: 1300px;
margin: 20px auto;
padding: 0 20px;
background: white; 
box-shadow: 0 0 10px rgba(0,0,0,0.05);
border-radius: 8px;
}

.brands-header {
padding: 20px 0;
border-bottom: 1px solid #eee;
}

.brands-header h2 {
font-size: 1.8rem;
font-weight: 700;
color: var(--cosmetics-text-dark);
margin-bottom: 5px;
}

.brand-count {
font-size: 0.9rem;
color: var(--cosmetics-text-light);
}

.alphabet-filter {
display: flex;
flex-wrap: wrap;
padding: 15px 0;
gap: 8px;
border-bottom: 1px solid #eee;
}

.alphabet-filter a {
text-decoration: none;
color: var(--cosmetics-text-dark);
font-weight: 600;
padding: 5px 10px;
border-radius: 4px;
transition: all 0.2s ease;
font-size: 0.9rem;
}

.alphabet-filter a.active {
color: white;
background-color: var(--cosmetics-accent-color);
box-shadow: 0 2px 4px rgba(233, 30, 99, 0.3);
}

.alphabet-filter a:not(.active):hover {
color: var(--cosmetics-accent-color);
background-color: #fce4ec; 
}

.alphabet-filter a.disabled {
color: #ccc;
pointer-events: none;
cursor: default;
}

.brands-list {
  
padding: 20px 0;
}

.brand-group {
margin-bottom: 30px;
}

.brand-group-header {
font-size: 2rem;
font-weight: 800;
color: var(--cosmetics-accent-color);
padding: 10px 0;
margin-bottom: 15px;
border-bottom: 2px solid #fce4ec; 
}

.brand-items-grid {
display: flex;
flex-wrap: wrap;
gap: 20px; 
}

.brand-item-text {
width: calc(20% - 16px); 
text-decoration: none;
display: flex; 
flex-direction: column; 
align-items: center; 
padding: 15px;
background: #fff;
border: 1px solid #ddd;
border-radius: 6px;
box-shadow: 0 1px 3px rgba(0,0,0,0.05);
transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
text-align: center;
height: auto; 
}

@media (max-width: 1000px) {
.brand-item-text {
width: calc(25% - 15px); 
}
}
@media (max-width: 768px) {
.brand-item-text {
width: calc(33.333% - 13.333px); 
}
}
@media (max-width: 480px) {
.brand-item-text {
width: calc(50% - 10px); 
}
}


.brand-item-text:hover {
transform: translateY(-3px);
box-shadow: 0 4px 8px rgba(233, 30, 99, 0.1);
border-color: var(--cosmetics-accent-color);
}

.brand-name {
font-size: 1.1rem;
font-weight: 700; 
color: var(--cosmetics-accent-color);
text-transform: uppercase;
margin-bottom: 5px; 
}

.brand-country {
font-size: 0.85rem;
font-weight: 500;
color: var(--cosmetics-text-light); 
margin-bottom: 10px; 
display: flex;
align-items: center;
gap: 5px;
}

.brand-country i {
color: #4CAF50; 
}

.brand-description {
font-size: 0.8rem;
color: var(--cosmetics-text-dark);
text-align: justify;
display: -webkit-box;
-webkit-line-clamp: 3; 
-webkit-box-orient: vertical;
overflow: hidden;
max-height: 3.6em; 
line-height: 1.2em;
font-style: italic;
background: #fafafa;
padding: 8px;
border-radius: 4px;
border-top: 1px solid #eee;
}
</style>

<div class="brands-page-container">
<div class="brands-header">
<h2>Danh sách Thương hiệu</h2>
  <p class="brand-count">Xem <?php echo count($brands); ?> thương hiệu hiện có</p>
</div>

<div class="alphabet-filter">
<?php
// Chỉ lấy A-Z, không hiển thị 0-9 trên thanh lọc (mặc dù vẫn nhóm 0-9)
$alphabet = array_merge(range('A', 'Z'));
foreach ($alphabet as $letter):
// Kiểm tra xem chữ cái này có thương hiệu nào không
$isActive = in_array($letter, $available_letters) ? '' : 'disabled';
$targetId = strtolower(str_replace('-', '', $letter)); // Tạo ID cho liên kết neo
?>
<a href="#<?php echo $targetId; ?>" class="<?php echo $isActive; ?>">
 <?php echo $letter; ?>
</a>
<?php endforeach; ?>
<?php
 // Thêm link 0-9
 $numeric_key = '0-9';
 $isNumericActive = in_array($numeric_key, $available_letters) ? '' : 'disabled';
 $numericTargetId = strtolower(str_replace('-', '', $numeric_key));
?>
<a href="#<?php echo $numericTargetId; ?>" class="<?php echo $isNumericActive; ?>">
 <?php echo $numeric_key; ?>
</a>
</div>

<div class="brands-list">
<?php foreach ($grouped_brands as $letter => $brands_list): ?>
<?php if (!empty($brands_list)): ?>
 <div class="brand-group" id="<?php echo strtolower(str_replace('-', '', $letter)); ?>">
 <h3 class="brand-group-header"><?php echo $letter; ?></h3>
 <div class="brand-items-grid">
       <?php foreach ($brands_list as $brand): ?>
 
     <a href="products.php?maTH=<?php echo htmlspecialchars($brand['maTH']); ?>" class="brand-item-text" title="<?php echo htmlspecialchars($brand['tenTH']); ?>">
  <span class="brand-name"><?php echo htmlspecialchars($brand['tenTH']); ?></span>
      
        <span class="brand-country">
      <i class="fa-solid fa-earth-americas"></i> 
      <?php echo htmlspecialchars($brand['quocGia'] ?? 'Chưa xác định'); ?>
    </span>

        <p class="brand-description">
      <?php echo htmlspecialchars($brand['moTa'] ?? 'Không có mô tả chi tiết.'); ?>
    </p>
 </a>
 <?php endforeach; ?>
 </div>
 </div>
<?php endif; ?>
<?php endforeach; ?>

  <?php if (empty($brands)): ?>
<p style="text-align: center; color: #888; padding: 50px;">Hiện chưa có thương hiệu nào được đăng ký.</p>
<?php endif; ?>
</div>
</div>

<script>
/* ... (GIỮ NGUYÊN PHẦN JAVASCRIPT) ... */
document.addEventListener('DOMContentLoaded', function() {
const alphabetLinks = document.querySelectorAll('.alphabet-filter a:not(.disabled)');

alphabetLinks.forEach(link => {
link.addEventListener('click', function(e) {
 // Lấy ID target từ href
 const targetId = this.getAttribute('href').substring(1);
 const targetElement = document.getElementById(targetId);

 if (targetElement) {
 e.preventDefault();
 // Cuộn đến phần tử, bù trừ cho header cố định (nếu có)
 const offset = targetElement.offsetTop - 70; 
 window.scrollTo({
 top: offset,
 behavior: 'smooth'
 });
 
 // Thêm class 'active' (Tùy chọn: giúp người dùng biết họ đã nhấn vào chữ cái nào)
 alphabetLinks.forEach(l => l.classList.remove('active'));
 this.classList.add('active');
 }
});
});

// Tùy chọn: Xóa active khi cuộn lên đầu
window.addEventListener('scroll', function() {
if (window.scrollY < 100) {
 alphabetLinks.forEach(l => l.classList.remove('active'));
}
});
});
</script>
<?php
require("footer.php");
?>