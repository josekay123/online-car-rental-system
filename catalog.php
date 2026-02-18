<?php
include 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';

$sql = "SELECT * FROM cars WHERE 1=1";

if ($search) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (make LIKE '%$search%' OR model LIKE '%$search%')";
}

if ($category != 'all') {
    $category = $conn->real_escape_string($category);
    $sql .= " AND category = '$category'";
}

if ($sort == 'price_asc') {
    $sql .= " ORDER BY price_per_day ASC";
} elseif ($sort == 'price_desc') {
    $sql .= " ORDER BY price_per_day DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

$result = $conn->query($sql);
$categories = ["Sedan", "SUV", "Luxury", "Sports", "Convertible", "Electric"];
?>

<?php include 'header.php'; ?>

<div class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold text-slate-900 mb-4">Our Fleet</h1>
        <p class="text-slate-500 text-lg max-w-2xl">
            Choose from our premium selection of vehicles. Whether you need speed, luxury, or space, we have the perfect ride for you.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-8 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form class="flex-grow relative" action="" method="GET">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Search by make or model..." value="<?php echo htmlspecialchars($search); ?>"
                   class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-colors">
            <?php if($category != 'all') echo '<input type="hidden" name="category" value="'.$category.'">'; ?>
            <?php if($sort != 'featured') echo '<input type="hidden" name="sort" value="'.$sort.'">'; ?>
        </form>
        
        <div class="flex gap-4">
            <form action="" method="GET">
                <?php if($search) echo '<input type="hidden" name="search" value="'.$search.'">'; ?>
                <?php if($sort != 'featured') echo '<input type="hidden" name="sort" value="'.$sort.'">'; ?>
                <select name="category" onchange="this.form.submit()" class="w-[180px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo $category == $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <form action="" method="GET">
                <?php if($search) echo '<input type="hidden" name="search" value="'.$search.'">'; ?>
                <?php if($category != 'all') echo '<input type="hidden" name="category" value="'.$category.'">'; ?>
                <select name="sort" onchange="this.form.submit()" class="w-[180px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="featured" <?php echo $sort == 'featured' ? 'selected' : ''; ?>>Featured</option>
                    <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="group bg-white rounded-2xl overflow-hidden border border-slate-200 hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                    <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['make']; ?>" 
                         class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/90 text-slate-900 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm shadow-sm">
                            <?php echo $row['category']; ?>
                        </span>
                    </div>
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                            <?php echo $row['make'] . ' ' . $row['model']; ?>
                        </h3>
                        <p class="text-slate-500 text-sm mt-1 line-clamp-2">
                            <?php echo $row['description']; ?>
                        </p>
                    </div>
                    
                    <!-- Car Specs Grid -->
                    <div class="grid grid-cols-2 gap-2 text-xs mb-4">
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-calendar text-indigo-500"></i>
                            <span><?php echo $row['year']; ?> Model</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-cog text-indigo-500"></i>
                            <span><?php echo $row['transmission']; ?></span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-gas-pump text-indigo-500"></i>
                            <span><?php echo $row['fuel_type']; ?></span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-users text-indigo-500"></i>
                            <span><?php echo $row['seats']; ?> Seats</span>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-slate-900">$<?php echo $row['price_per_day']; ?></span>
                            <span class="text-slate-500 text-sm font-medium">/day</span>
                        </div>
                        <a href="book.php?id=<?php echo $row['id']; ?>" 
                           class="bg-slate-900 hover:bg-indigo-600 text-white px-6 py-2 rounded-full text-sm font-medium transition-all shadow-lg shadow-slate-200 hover:shadow-indigo-200">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        
        <?php if($result->num_rows == 0): ?>
            <div class="col-span-full text-center py-24">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <i class="fas fa-search text-slate-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-900">No cars found</h3>
                <p class="text-slate-500 mt-2">Try adjusting your filters or search terms.</p>
                <a href="catalog.php" class="inline-block mt-6 px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                    Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
