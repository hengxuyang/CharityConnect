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
      {
        id: uuidv4(),
        name: 'Canned Food',
        category_id: 1, // Food
        unit: 'can',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'T-Shirts',
        category_id: 2, // Clothing
        unit: 'piece',
        created_at: new Date()
      },
      {
        id: uuidv4(),
        name: 'Soap',
        category_id: 3, // Hygiene
        unit: 'bar',
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
