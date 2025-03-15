const { sequelize } = require('../models');
const { User, Charity, Category, Item } = require('../models');
const bcrypt = require('bcrypt');
const { v4: uuidv4 } = require('uuid');

// Define a constant salt rounds value for consistency with User model
const SALT_ROUNDS = 10;

// Function to initialize the database
const initializeDatabase = async () => {
  try {
    // Sync all models with the database
    await sequelize.sync({ force: true });
    console.log('Database synchronized successfully');

    // Seed categories
    const categories = await Category.bulkCreate([
      { name: 'Food' },
      { name: 'Clothing' },
      { name: 'Hygiene' },
      { name: 'Medical' },
      { name: 'Education' },
      { name: 'Furniture' },
      { name: 'Electronics' },
      { name: 'Toys' },
      { name: 'Other' }
    ]);
    console.log('Categories seeded successfully');

    // Create admin user
    const adminUser = await User.create({
      id: uuidv4(),
      name: 'Admin User',
      email: 'admin@example.com',
      password: 'admin123',
      role: 'admin',
      created_at: new Date()
    });
    console.log('Admin user created successfully');

    // Create sample charity
    const charityId = uuidv4();
    const charity = await Charity.create({
      id: charityId,
      name: 'Sample Charity',
      email: 'charity@example.com',
      phone: '123-456-7890',
      address: '123 Charity St, City, Country',
      status: 'approved',
      created_at: new Date()
    });

    // Create charity user
    const charityUser = await User.create({
      id: uuidv4(),
      name: 'Charity User',
      email: 'charity@example.com',
      password: 'charity123',
      role: 'charity',
      charity_id: charityId,
      created_at: new Date()
    });
    console.log('Sample charity and user created successfully');

    // Create sample donor user
    const donorUser = await User.create({
      id: uuidv4(),
      name: 'Donor User',
      email: 'donor@example.com',
      password: 'donor123',
      role: 'public_user',
      created_at: new Date()
    });
    console.log('Sample donor user created successfully');

    // Create sample items
    const items = await Item.bulkCreate([
      // Food items
      {
        id: uuidv4(),
        name: 'Canned Food',
        category_id: 1,
        unit: 'can',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Rice',
        category_id: 1,
        unit: 'kg',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Pasta',
        category_id: 1,
        unit: 'packet',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Cooking Oil',
        category_id: 1,
        unit: 'bottle',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Baby Formula',
        category_id: 1,
        unit: 'tin',
        created_at: new Date()
      },
      
      // Clothing items
      {
        id: uuidv4(),
        name: 'T-Shirts',
        category_id: 2,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Jackets',
        category_id: 2,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Pants',
        category_id: 2,
        unit: 'pair',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Children\'s Clothing',
        category_id: 2,
        unit: 'set',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Shoes',
        category_id: 2,
        unit: 'pair',
        created_at: new Date()
      },
      
      // Hygiene items
      {
        id: uuidv4(),
        name: 'Soap',
        category_id: 3,
        unit: 'bar',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Toothpaste',
        category_id: 3,
        unit: 'tube',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Shampoo',
        category_id: 3,
        unit: 'bottle',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Sanitary Pads',
        category_id: 3,
        unit: 'pack',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Diapers',
        category_id: 3,
        unit: 'pack',
        created_at: new Date()
      },
      
      // Medical items
      {
        id: uuidv4(),
        name: 'First Aid Kits',
        category_id: 4,
        unit: 'kit',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Bandages',
        category_id: 4,
        unit: 'box',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Vitamins',
        category_id: 4,
        unit: 'bottle',
        created_at: new Date()
      },
      
      // Education items
      {
        id: uuidv4(),
        name: 'Notebooks',
        category_id: 5,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Textbooks',
        category_id: 5,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Stationery Sets',
        category_id: 5,
        unit: 'set',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Backpacks',
        category_id: 5,
        unit: 'piece',
        created_at: new Date()
      },
      
      // Furniture items
      {
        id: uuidv4(),
        name: 'Beds',
        category_id: 6,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Tables',
        category_id: 6,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Chairs',
        category_id: 6,
        unit: 'piece',
        created_at: new Date()
      },
      
      // Electronics items
      {
        id: uuidv4(),
        name: 'Laptops',
        category_id: 7,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Mobile Phones',
        category_id: 7,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Tablets',
        category_id: 7,
        unit: 'piece',
        created_at: new Date()
      },
      
      // Toys items
      {
        id: uuidv4(),
        name: 'Board Games',
        category_id: 8,
        unit: 'set',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Stuffed Animals',
        category_id: 8,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Educational Toys',
        category_id: 8,
        unit: 'set',
        created_at: new Date()
      },
      
      // Other items
      {
        id: uuidv4(),
        name: 'Blankets',
        category_id: 9,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Water Bottles',
        category_id: 9,
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Face Masks',
        category_id: 9,
        unit: 'box',
        created_at: new Date()
      }
    ]);
    console.log('Sample items created successfully');

    console.log('Database initialization completed successfully');
  } catch (error) {
    console.error('Error initializing database:', error);
  }
};

// Run the initialization if this script is executed directly
if (require.main === module) {
  initializeDatabase()
    .then(() => {
      console.log('Database initialization script completed');
      process.exit(0);
    })
    .catch((error) => {
      console.error('Database initialization script failed:', error);
      process.exit(1);
    });
}

module.exports = { initializeDatabase };
