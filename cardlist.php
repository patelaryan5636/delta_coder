<?php
session_start();
require_once 'includes/scripts/connection.php';
if(isset($_SESSION['pacpal_logedin_user_id']) && (trim ($_SESSION['pacpal_logedin_user_id']) !== '')){
  $user_id = $_SESSION['pacpal_logedin_user_id'];
  $query = "SELECT * FROM user_master WHERE user_id = $user_id";
  $result = mysqli_query($conn, $query);
  $userdata = mysqli_fetch_assoc($result);
  $user_role = $userdata["user_role"];
  if($user_role != 3){
      header("Location: 404.php");
  }
} else {
  header("Location: sign-in.php");
}   
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Groups - PackPal</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#96b4b4', secondary: '#c4dfdf' }, borderRadius: { 'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px', 'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px' } } } }</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.cdnfonts.com/css/agbalumo');

        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        body {
            background-color: #f8fafc;
            color: #333;
            font-family: 'Inter', sans-serif;
        }

        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
            border-color: rgba(118, 181, 197, 0.2);
        }

        .btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            outline: none;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #76b5c5;
            box-shadow: 0 0 0 3px rgba(118, 181, 197, 0.1);
        }

        .status-badge {
            font-weight: 500;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .filter-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .no-result {
            font-family: 'Agbalumo', sans-serif;
            font-size: 4vw;
            color: #558490;

            display: none;
            /* Hidden by default */
            grid-column: 1 / -1;
            /* Span all columns */
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 backdrop-blur-sm bg-white/90">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 sm:h-16">
                <div class="flex items-center">
                    <a href="index" class="text-2xl font-['Pacifico'] text-primary">Packpal </a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative w-10 h-10 flex items-center justify-center rounded-full bg-gray-100">
                        <i class="ri-user-line text-gray-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Header -->
        <div
            class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 bg-gradient-to-r from-primary/10 to-transparent p-4 sm:p-6 rounded-lg">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">My Groups</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your packing groups and travel plans</p>
            </div>
            <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="ri-search-line text-gray-400"></i>
                    </div>
                    <input type="text"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-button pl-10 pr-4 py-2.5 w-full sm:min-w-[16rem]"
                        placeholder="Search groups...">
                </div>
                <a href="form"
                    class="bg-primary text-white px-4 py-2.5 rounded-button flex items-center justify-center whitespace-nowrap shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 transition-all duration-300">
                    <i class="ri-add-line mr-2"></i>
                    Add New Group
                </a>
            </div>
        </div>
        <!-- Group Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <h1 class="no-result">No Group Found</h1>
            <?php
        $result = $conn->query("SELECT * FROM user_group_roles where user_id = '$user_id'");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $group_id = $row['group_id'];

                $sql = "SELECT * FROM group_master WHERE group_id = '$group_id'";
                $result1 = $conn->query($sql); 
                if ($result1->num_rows > 0) {
                    $row1 = $result1->fetch_assoc();
                    $groupName = $row1['group_name'];
                    $ownerId = $row1['created_by'];
                    $createdOn = date("F j, Y", strtotime($row1['created_at']));
                    $status = isset($row1['status']) ? $row1['status'] : 'ACTIVE'; // fallback
                } else {
                    continue; // Skip if no group found
                }   
            
          
              // Fetch owner's name
              $ownerName = "Unknown";
              $result2 = $conn->query("SELECT user_name FROM user_master WHERE user_id = '$ownerId'");
              if ($result2 && $result2->num_rows > 0) {
                $row2 = $result2->fetch_assoc();
                $ownerName = $row2['user_name'];
              }
          ?>

            <div class="card bg-white rounded-lg shadow-sm overflow-hidden backdrop-blur-sm bg-white/90">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-0 mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">
                            <?php echo htmlspecialchars($groupName); ?>
                        </h3>
                        <a href="generate_task_report.php?group_id=<?= $group_id ?>"
                            class="status-badge text-xs px-2.5 py-1 rounded-full shadow-sm shadow-green-100/50">
                            <img src="./assets/img/pdf.svg" alt="pdf" style="height: 24px;">
                        </span>
                    </div>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center">
                            <div class="w-5 h-5 flex items-center justify-center text-gray-500">
                                <i class="ri-user-line"></i>
                            </div>
                            <span class="text-gray-600 text-sm ml-2">Created by: <span class="font-medium">
                                    <?php echo htmlspecialchars($ownerName); ?>
                                </span></span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-5 h-5 flex items-center justify-center text-gray-500">
                                <i class="ri-calendar-line"></i>
                            </div>
                            <span class="text-gray-600 text-sm ml-2">Created on: <span class="font-medium">
                                    <?php echo htmlspecialchars($createdOn); ?>
                                </span></span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-5 h-5 flex items-center justify-center text-gray-500">
                                <i class="ri-user-settings-line"></i>
                            </div>
                            <span class="text-gray-600 text-sm ml-2">My Role: <span
                                    class="font-medium">Admin</span></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <?php
                        $roleQ = $conn->query("SELECT role FROM user_group_roles WHERE user_id = '$user_id' AND group_id = '$group_id'");
                        $roleRow = $roleQ->fetch_assoc();
                        $role = strtolower($roleRow['role']);
                        $isPrivileged = ($role === 'owner' || $role === 'admin');
                        ?>

                        <!-- Create Checklist Button -->
                        <?php if ($isPrivileged): ?>
                            <a href="assign_checklist.php?group_id=<?= $group_id ?>"
                                class="btn bg-primary text-white px-2 py-2 rounded-button flex items-center whitespace-nowrap shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30">
                                <i class="fa-solid fa-list-check"></i>&nbsp;Create Checklist
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0)"
                                class="btn bg-gray-200 text-gray-400 px-2 py-2 rounded-button flex items-center whitespace-nowrap cursor-not-allowed"
                                title="Only Owner or Admin can create checklist">
                                <i class="fa-solid fa-list-check"></i>&nbsp;Create Checklist
                            </a>
                        <?php endif; ?>
                        
                        <!-- Add Category Button -->
                        <div class="flex space-x-2">
                            <?php if ($isPrivileged): ?>
                                <a href="add_category.php?group_id=<?= $group_id ?>"
                                    class="btn bg-orange-50 text-red-600 w-9 h-9 rounded-button flex items-center justify-center">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            <?php else: ?>
                                <a href="javascript:void(0)"
                                    class="btn bg-gray-200 text-gray-400 w-9 h-9 rounded-button flex items-center justify-center cursor-not-allowed"
                                    title="Only Owner or Admin can add categories">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                            <?php
                                $roleQ = $conn->query("SELECT role FROM user_group_roles WHERE user_id = '$user_id' AND group_id = '$group_id'");
                                $roleRow = $roleQ->fetch_assoc();

                                $isOwner = false;

                                if ($roleRow && strtolower($roleRow['role']) === 'owner') {
                                    $isOwner = true;
                                }
                            ?>
                            <a 
                                href="<?= $isOwner ? "editgroup.php?id=$group_id" : 'javascript:void(0);' ?>" 
                                class="btn <?= $isOwner ? 'bg-gray-100 text-gray-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed' ?> w-9 h-9 rounded-button flex items-center justify-center"
                                <?= $isOwner ? '' : 'onclick="return false;" title=\'Only the Owner can edit\'' ?>
                            >
                                <i class="ri-edit-line"></i>
                            </a>
                           

                            <?php
                            $roleQ = $conn->query("SELECT role FROM user_group_roles WHERE user_id = '$user_id' AND group_id = '$group_id'");
                            $roleRow = $roleQ->fetch_assoc();

                            $redirectURL = "checklist_view.php?group_id=$group_id"; // default

                            if ($roleRow) {
                                $role = strtolower($roleRow['role']);
                                if ($role === 'viewer') {
                                    $redirectURL = "checklist_viewer_view.php?group_id=$group_id";
                                } elseif ($role === 'member') {
                                    $redirectURL = "checklist_member_view.php?group_id=$group_id";
                                }
                            }
                            ?>
                            <a href="<?= $redirectURL ?>"
                                class="btn bg-gray-100 text-gray-700 w-9 h-9 rounded-button flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="<?= $isOwner ? 'delete_group.php?group_id=' . $group_id : 'javascript:void(0);' ?>"
                               <?= $isOwner ? '' : 'title="Only the Owner can delete"' ?>
                               class="btn w-9 h-9 rounded-button flex items-center justify-center
                                      <?= $isOwner ? 'bg-red-50 text-red-600' : 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none' ?>">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                            
                            <!-- <button
                                class="btn bg-red-50 text-red-600 w-9 h-9 rounded-button flex items-center justify-center">
                                <i class="ri-delete-bin-line"></i>
                            </button> -->
                        </div>
                    </div>
                </div>
                
                <?php
            }
        }
        ?>
    </div>
            <!-- Card 1 -->
        </div>

    </main>
    <!-- Pagination -->
    <div class="flex w-[80%] mx-auto mb-4 flex-col sm:flex-row items-center justify-between gap-4">
        <p id="pagination-info" class="text-sm text-gray-600 order-2 sm:order-1">Showing <span
                class="font-medium">1-6</span> of <span class="font-medium">9</span> groups</p>
        <div class="flex items-center space-x-2 order-1 sm:order-2">
            <button id="prev-page"
                class="px-3 py-2 rounded-button bg-white border border-gray-300 text-gray-500 flex items-center justify-center hover:bg-gray-50 hover:border-gray-400 transition-all duration-300">
                <i class="ri-arrow-left-s-line"></i>
            </button>
            <button id="page-1"
                class="w-9 h-9 rounded-button bg-primary text-white flex items-center justify-center"></button>
            <button id="next-page"
                class="px-3 py-2 rounded-button bg-white border border-gray-300 text-gray-700 flex items-center justify-center">
                <i class="ri-arrow-right-s-line"></i>
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Configuration
            const cardsPerPage = 9;
            const cardContainer = document.querySelector('.card-container'); // Wrap your cards in this container
            const paginationInfo = document.getElementById('pagination-info');
            const prevButton = document.getElementById('prev-page');
            const nextButton = document.getElementById('next-page');
            const currentPageButton = document.getElementById('page-1'); // This will show current page number

            // Get all card elements
            const allCards = Array.from(document.querySelectorAll('.card'));
            const totalCards = allCards.length;
            const totalPages = Math.ceil(totalCards / cardsPerPage);

            let currentPage = 1;

            // Initialize pagination
            function initPagination() {
                // Hide all cards initially
                allCards.forEach(card => card.style.display = 'none');

                // Show cards for the first page
                showPage(currentPage);

                // Update pagination info
                updatePaginationInfo();

                // Set up event listeners
                prevButton.addEventListener('click', goToPrevPage);
                nextButton.addEventListener('click', goToNextPage);

                // Disable prev button on first page
                updateButtonStates();
            }

            // Show cards for a specific page
            function showPage(page) {
                const startIndex = (page - 1) * cardsPerPage;
                const endIndex = Math.min(startIndex + cardsPerPage, totalCards);

                // Hide all cards first
                allCards.forEach(card => card.style.display = 'none');

                // Show cards for the current page
                for (let i = startIndex; i < endIndex; i++) {
                    if (allCards[i]) {
                        allCards[i].style.display = 'block';
                    }
                }

                // Update current page number display
                currentPageButton.textContent = page;
            }

            // Update pagination information text
            function updatePaginationInfo() {
                const start = ((currentPage - 1) * cardsPerPage) + 1;
                const end = Math.min(currentPage * cardsPerPage, totalCards);
                paginationInfo.innerHTML = `Showing <span class="font-medium">${start}-${end}</span> of <span class="font-medium">${totalCards}</span> groups`;
            }

            // Update button states (disable/enable)
            function updateButtonStates() {
                prevButton.disabled = currentPage === 1;
                nextButton.disabled = currentPage === totalPages;

                // Visual feedback for disabled state
                if (prevButton.disabled) {
                    prevButton.classList.add('opacity-50', 'cursor-not-allowed');
                    prevButton.classList.remove('hover:bg-gray-50', 'hover:border-gray-400');
                } else {
                    prevButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    prevButton.classList.add('hover:bg-gray-50', 'hover:border-gray-400');
                }

                if (nextButton.disabled) {
                    nextButton.classList.add('opacity-50', 'cursor-not-allowed');
                    nextButton.classList.remove('hover:bg-gray-50', 'hover:border-gray-400');
                } else {
                    nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    nextButton.classList.add('hover:bg-gray-50', 'hover:border-gray-400');
                }
            }

            // Navigation functions
            function goToPage(pageNumber) {
                if (pageNumber < 1 || pageNumber > totalPages || pageNumber === currentPage) return;

                currentPage = pageNumber;
                showPage(currentPage);
                updatePaginationInfo();
                updateButtonStates();
            }

            function goToPrevPage() {
                if (currentPage > 1) {
                    goToPage(currentPage - 1);
                }
            }

            function goToNextPage() {
                if (currentPage < totalPages) {
                    goToPage(currentPage + 1);
                }
            }

            // Initialize the pagination
            initPagination();
        });


        document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const searchInput = document.querySelector('input[type="text"][placeholder="Search groups..."]');
    const allCards = Array.from(document.querySelectorAll('.card'));
    const noResultMessage = document.querySelector('.no-result');
    const cardGrid = document.querySelector('.grid');
    const paginationInfo = document.getElementById('pagination-info');
    
    // Store original state
    const originalCards = [...allCards];
    let currentCards = [...allCards];
    
    // Initialize search
    function initSearch() {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            filterCards(searchTerm);
        });
    }
    
    // Filter cards based on search term
    function filterCards(searchTerm = '') {
        if (searchTerm === '') {
            // Reset to original state
            resetCards();
            noResultMessage.style.display = 'none';
            return;
        }
        
        // Filter cards
        const filteredCards = originalCards.filter(card => {
            const cardText = card.textContent.toLowerCase();
            return cardText.includes(searchTerm);
        });
        
        // Update display
        updateCardDisplay(filteredCards);
        
        // Show/hide no results message
        noResultMessage.style.display = filteredCards.length === 0 ? 'block' : 'none';
        
        // Update current cards reference
        currentCards = filteredCards;
        
        // Update pagination info
        updatePaginationInfo();
        
        // Reinitialize pagination if exists
        if (typeof initPagination === 'function') {
            initPagination();
        }
    }
    
    // Reset cards to original state
    function resetCards() {
        // Remove all cards first
        allCards.forEach(card => card.remove());
        
        // Add back original cards in original order
        originalCards.forEach(card => {
            card.style.display = 'block';
            cardGrid.appendChild(card);
        });
        
        currentCards = [...originalCards];
    }
    
    // Update card display
    function updateCardDisplay(filteredCards) {
        // Hide all cards first
        allCards.forEach(card => card.style.display = 'none');
        
        // Show filtered cards
        filteredCards.forEach(card => {
            card.style.display = 'block';
            cardGrid.appendChild(card);
        });
    }
    
    // Update pagination info
    function updatePaginationInfo() {
        if (!paginationInfo) return;
        
        const total = currentCards.length;
        const showing = Math.min(total, 1); // Adjust based on your pagination logic
        paginationInfo.innerHTML = `Showing <span class="font-medium">1-${showing}</span> of <span class="font-medium">${total}</span> groups`;
    }
    
    // Initialize search functionality
    initSearch();
    
    // Hide no result message initially if there are cards
    if (allCards.length > 0) {
        noResultMessage.style.display = 'none';
    } else {
        noResultMessage.style.display = 'block';
    }
});
    </script>
</body>

</html>