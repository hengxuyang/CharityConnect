const { v4: uuidv4 } = require('uuid');

module.exports = (sequelize, DataTypes) => {
  const Item = sequelize.define('Item', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false
    },
    category_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    unit: {
      type: DataTypes.STRING,
      allowNull: false
    },
    expiry_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'items'
  });

  Item.associate = (models) => {
    Item.belongsTo(models.Category, {
      foreignKey: 'category_id',
      as: 'category'
    });
    Item.hasMany(models.Request, {
      foreignKey: 'item_id',
      as: 'requests'
    });
    Item.hasMany(models.Inventory, {
      foreignKey: 'item_id',
      as: 'inventory'
    });
  };

  return Item;
};
