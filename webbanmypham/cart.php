<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("header.php");
require_once("config.php");

// --- 1. XỬ LÝ LINK QUAY LẠI THÔNG MINH ---
$backUrl = "products.php"; 
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'products.php') !== false) {
    $backUrl = $_SERVER['HTTP_REFERER'];
    $_SESSION['last_shopping_url'] = $backUrl;
} elseif (isset($_SESSION['last_shopping_url'])) {
    $backUrl = $_SESSION['last_shopping_url'];
}

// --- 2. KIỂM TRA ĐĂNG NHẬP ---
if (!isset($_SESSION['tenDangnhap'])) {
    echo "<div class='container' style='padding: 100px 20px; text-align: center;'>
            <h2 style='color: #E91E63;'>Vui lòng đăng nhập để xem giỏ hàng</h2>
            <a href='login.php' class='btn-pink' style='display: inline-block; margin-top: 20px; padding: 10px 25px; background: #E91E63; color: white; text-decoration: none; border-radius: 50px;'>Đăng nhập ngay</a>
          </div>";
    require("footer.php");
    exit();
}

$tenDangNhap = $_SESSION['tenDangnhap'];
$cart_items = [];
$total_bill = 0;

// --- 3. TRUY VẤN DỮ LIỆU ---
$sql = "SELECT ct.id AS maCTGH, m.maMH, m.tenMH, m.hinhAnh, ct.dongia, ct.soluong, m.soLuongTon 
        FROM giohang g
        JOIN chitietgiohang ct ON g.id = ct.giohang_id
        JOIN mathang m ON ct.maMH = m.maMH
        WHERE g.tenDangNhap = '$tenDangNhap' AND g.trangthai = 0";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_bill += $row['dongia'] * $row['soluong'];
    }
}
?>

<style>
    :root { --primary-color: #E91E63; --bg-light: #fff8fb; }
    .cart-container { max-width: 1100px; margin: 40px auto; padding: 20px; min-height: 60vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .cart-title { color: var(--primary-color); font-weight: 700; margin-bottom: 30px; border-bottom: 2px solid #ffe1ec; padding-bottom: 10px; }
    .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .cart-table th { background-color: var(--bg-light); color: var(--primary-color); padding: 15px; text-align: left; }
    .cart-table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
    .product-img { width: 80px; height: 80px; object-fit: contain; border-radius: 8px; border: 1px solid #eee; background: #fff; }
    
    .input-qty { width: 70px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; text-align: center; font-weight: bold; }
    .stock-warning { font-size: 0.75rem; color: #ff5252; display: block; margin-top: 5px; }

    .btn-delete { color: #ff5252; text-decoration: none; font-size: 1.2rem; transition: 0.3s; padding: 10px; }
    .btn-delete:hover { color: #b71c1c; transform: scale(1.2); }

    .cart-summary { margin-top: 30px; background: #fff; padding: 25px; border-radius: 12px; text-align: right; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .total-price-label { font-size: 1.6rem; color: var(--primary-color); font-weight: 700; margin-bottom: 20px; }
    .btn-checkout { background: var(--primary-color); color: white; padding: 15px 45px; border-radius: 50px; text-decoration: none; font-weight: 600; display: inline-block; transition: 0.3s; border: none; cursor: pointer; }
    .btn-checkout:hover { background: #c2185b; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3); }
    .btn-back { color: #888; text-decoration: none; font-weight: 500; transition: 0.3s; }
    .btn-back:hover { color: var(--primary-color); }
</style>

<div class="cart-container">
    <h2 class="cart-title"><i class="fa fa-shopping-cart"></i> Giỏ hàng của bạn</h2>

    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 50px;">
            <p style="font-size: 1.2rem; color: #888;">Giỏ hàng của bạn đang trống.</p>
            <a href="products.php" class="btn-back" style="margin-top: 20px; display: inline-block;">&larr; Quay lại cửa hàng</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th style="text-align: center;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr class="cart-row" data-id="<?php echo $item['maCTGH']; ?>" data-price="<?php echo $item['dongia']; ?>">
                        <td>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <img src="<?php echo htmlspecialchars($item['hinhAnh']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['tenMH']); ?>" 
                                     class="product-img"
                                     onerror="this.src='https://via.placeholder.com/80?text=No+Image';">
                                <div>
                                    <strong><?php echo htmlspecialchars($item['tenMH']); ?></strong>
                                    <span class="stock-warning">Kho còn: <?php echo $item['soLuongTon']; ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?php echo number_format($item['dongia'], 0, ',', '.'); ?>₫</td>
                        <td>
                            <input type="number" 
                                   class="input-qty" 
                                   value="<?php echo $item['soluong']; ?>" 
                                   min="1" 
                                   max="<?php echo $item['soLuongTon']; ?>"
                                   data-stock="<?php echo $item['soLuongTon']; ?>"
                                   onchange="updateQuantity(this, <?php echo $item['maCTGH']; ?>)">
                        </td>
                        <td>
                            <strong class="row-total" style="color: var(--primary-color);">
                                <?php echo number_format($item['dongia'] * $item['soluong'], 0, ',', '.'); ?>₫
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            <a href="delete_from_cart.php?id=<?php echo $item['maCTGH']; ?>" class="btn-delete" onclick="return confirm('Xóa sản phẩm này khỏi giỏ?')">
                                <i class="fa fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="total-price-label">Tổng cộng: <span id="grand-total"><?php echo number_format($total_bill, 0, ',', '.'); ?></span>₫</div>
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 25px;">
                <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn-back">&larr; Tiếp tục mua sắm</a>
                <a href="checkout.php" class="btn-checkout">TIẾN HÀNH THANH TOÁN</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// (Giữ nguyên phần JavaScript cũ của bạn)
function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
}

function updateQuantity(inputElement, cartDetailId) {
    let newQty = parseInt(inputElement.value);
    let stock = parseInt(inputElement.getAttribute('data-stock'));
    
    if (newQty > stock) {
        alert("Rất tiếc, số lượng trong kho chỉ còn " + stock + " sản phẩm.");
        newQty = stock;
        inputElement.value = stock;
    }

    if (newQty < 1 || isNaN(newQty)) {
        newQty = 1;
        inputElement.value = 1;
    }

    let row = inputElement.closest('.cart-row');
    let price = parseFloat(row.getAttribute('data-price'));
    let rowTotalElement = row.querySelector('.row-total');

    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_ctgh=${cartDetailId}&soLuong=${newQty}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'error') {
            alert(data.message);
            inputElement.value = data.current_stock;
            newQty = data.current_stock;
        }
        let newRowTotal = price * newQty;
        rowTotalElement.innerText = formatNumber(newRowTotal) + '₫';
        calculateGrandTotal();
    })
    .catch(error => console.error('Lỗi hệ thống:', error));
}

function calculateGrandTotal() {
    let allRows = document.querySelectorAll('.cart-row');
    let total = 0;
    allRows.forEach(row => {
        let price = parseFloat(row.getAttribute('data-price'));
        let qty = parseInt(row.querySelector('.input-qty').value);
        total += price * qty;
    });
    document.getElementById('grand-total').innerText = formatNumber(total);
}
</script>

<?php require("footer.php"); ?>