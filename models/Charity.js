const { v4: uuidv4 } = require('uuid');

module.exports = (sequelize, DataTypes) => {
  const Charity = sequelize.define('Charity', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false
    },
    email: {
      type: DataTypes.STRING,
      allowNull: false,
      unique: true,
      validate: {
        isEmail: true
      }
    },
    phone: {
      type: DataTypes.STRING,
      allowNull: false
    },
    address: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    status: {
      type: DataTypes.ENUM('pending', 'approved', 'rejected'),
      allowNull: false,
      defaultValue: 'pending'
    },
    created_at: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'charities'
  });

  Charity.associate = (models) => {
    Charity.hasMany(models.User, {
      foreignKey: 'charity_id',
      as: 'users'
    });
    Charity.hasMany(models.Request, {
      foreignKey: 'charity_id',
      as: 'requests'
    });
    Charity.hasMany(models.Inventory, {
      foreignKey: 'charity_id',
      as: 'inventory'
    });
    Charity.hasMany(models.Notification, {
      foreignKey: 'charity_id',
      as: 'notifications'
    });
  };

  return Charity;
};
