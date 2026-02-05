# Finder Fluent API

A modern, intuitive API for working with the Twinfield Finder. Inspired by Laravel's Query Builder and modern JavaScript libraries.

## ✨ Benefits

- **Chainable methods** - Chain methods together for readable code
- **Type-safe** - Each entity has specific methods available
- **IDE autocomplete** - Full support for code completion
- **Self-documenting** - Code is easy to read and understand
- **Backward compatible** - Old API remains fully functional

## 🚀 Quick Start

### Retrieving Offices

```php
$finder = $client->get_finder();

// Simple: all offices
$offices = $finder->offices()->get_offices();

// With options
$offices = $finder->offices()
    ->include_id()
    ->pattern('Amsterdam*')
    ->limit(50)
    ->get_offices();
```

### Retrieving Dimensions

```php
// Customers (debtors)
$customers = $finder->dimensions()
    ->customers()
    ->limit(100)
    ->items();

// Suppliers (creditors)
$suppliers = $finder->dimensions()
    ->suppliers()
    ->pattern('A*')
    ->items();

// Cost centers
$cost_centers = $finder->dimensions()
    ->cost_centers()
    ->items();

// Fixed assets
$fixed_assets = $finder->dimensions()
    ->fixed_assets()
    ->items();

// Projects
$projects = $finder->dimensions()
    ->projects()
    ->items();
```

### Advanced Filters

```php
// Dimensions modified since 1 year ago
$recent = $finder->dimensions()
    ->customers()
    ->modified_since('-1 year')
    ->items();

// With DateTime object
$date = new DateTimeImmutable('2025-01-01');
$recent = $finder->dimensions()
    ->customers()
    ->modified_since($date)
    ->items();

// Including hidden dimensions
$all = $finder->dimensions()
    ->customers()
    ->include_hidden()
    ->items();

// For specific office
$office_customers = $finder->dimensions()
    ->customers()
    ->office('1000')
    ->items();

// For specific company
$company_customers = $finder->dimensions()
    ->customers()
    ->company('*')
    ->items();
```

### Retrieving Articles/Items

```php
$articles = $finder->articles()
    ->pattern('*laptop*')
    ->limit(25)
    ->items();
```

### General Ledger Accounts

```php
$gl_accounts = $finder->generalLedger()
    ->include_all_types()
    ->items();
```

## 📖 Available Methods

### Base Methods (for all entities)

- `pattern(string $pattern)` - Search pattern with wildcards (* and ?)
- `where(string $pattern)` - Alias for `pattern()`
- `searchField(int $field)` - Search in specific field (use `SearchFields` constants)
- `offset(int $first_row)` - Start from specific row
- `limit(int $max_rows)` - Maximum number of results
- `paginate(int $page, int $per_page)` - Set pagination
- `option(string $key, string $value)` - Add custom option
- `office(string $office_code)` - Filter by office

### Result Methods

- `get()` - Retrieve `FinderData` object
- `items()` - Retrieve array of items
- `first()` - Retrieve first item
- `count()` - Count total number of results
- `execute()` - Execute query and get `SearchResponse`

### Office-specific Methods

**`OfficeQueryBuilder`**
- `include_id()` - Add office ID to results
- `get_offices()` - Retrieve array of `Office` objects

### Dimension-specific Methods

**`DimensionQueryBuilder`**
- `type(string $type)` - Set dimension type ('DEB', 'CRD', etc.)
- `customers()` - Shortcut for type('DEB')
- `suppliers()` - Shortcut for type('CRD')
- `cost_centers()` - Shortcut for type('KPL')
- `fixed_assets()` - Shortcut for type('AST')
- `projects()` - Shortcut for type('PRJ')
- `include_hidden()` - Include hidden dimensions
- `modified_since(DateTimeInterface|string $date)` - Filter by modification date
- `company(string $company)` - Filter by company

### General Ledger-specific Methods

**`GeneralLedgerQueryBuilder`**
- `include_all_types()` - Include all general ledger account types

## 🔧 Advanced Usage

### Pagination

```php
// Page 1, 25 items per page
$page1 = $finder->dimensions()
    ->customers()
    ->paginate(page: 1, per_page: 25)
    ->items();

// Page 2
$page2 = $finder->dimensions()
    ->customers()
    ->paginate(page: 2, per_page: 25)
    ->items();

// Or manually with offset/limit
$results = $finder->dimensions()
    ->customers()
    ->offset(51)
    ->limit(25)
    ->items();
```

### Searching in Specific Fields

```php
use Pronamic\WordPress\Twinfield\Finder\SearchFields;

$results = $finder->dimensions()
    ->customers()
    ->pattern('John*')
    ->searchField(SearchFields::NAME)
    ->items();
```

### Only Counting Without Retrieving Data

```php
$total = $finder->dimensions()
    ->customers()
    ->count();

echo "Total number of customers: {$total}";
```

### Retrieving First Result

```php
$first_customer = $finder->dimensions()
    ->customers()
    ->pattern('A*')
    ->first();
```

### Custom Finder Types

For finder types without a specific builder:

```php
use Pronamic\WordPress\Twinfield\Finder\FinderTypes;

$results = $finder->query(FinderTypes::BNK) // Banks
    ->limit(50)
    ->items();
```

## 🎯 A Builder For Each Entity?

Yes! Each entity has its own builder class with **only the options that are relevant**:

- **`OfficeQueryBuilder`** - For offices (with `include_id()`)
- **`DimensionQueryBuilder`** - For dimensions (with `customers()`, `modified_since()`, etc.)
- **`ArticleQueryBuilder`** - For articles/items
- **`GeneralLedgerQueryBuilder`** - For general ledger accounts (with `include_all_types()`)

For other finder types without a specific builder, you can use the generic `query()` method.

## 🔄 Backward Compatibility

The old API still works completely:

```php
// Old API
$search = new Search(
    FinderTypes::OFF,
    '*',
    0,
    1,
    100,
    ['includeid' => '1']
);
$response = $finder->search($search);

// New API (equivalent)
$offices = $finder->offices()
    ->include_id()
    ->limit(100)
    ->get_offices();
```

## 📚 More Information

- [Twinfield Finder API Documentation](https://developers.twinfield.com/)
- [FinderTypes constants](../src/Finder/FinderTypes.php)
- [SearchFields constants](../src/Finder/SearchFields.php)
