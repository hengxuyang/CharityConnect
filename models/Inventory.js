const { v4: uuidv4 } = require('uuid');

module.exports = (sequelize, DataTypes) => {
  const Inventory = sequelize.define('Inventory', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    charity_id: {
      type: DataTypes.UUID,
      allowNull: false
    },
    item_id: {
      type: DataTypes.UUID,
      allowNull: false
    },
    quantity: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
      validate: {
        min: 0
      }
    },
    last_updated: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'inventory'
  });

  Inventory.associate = (models) => {
    Inventory.belongsTo(models.Charity, {
      foreignKey: 'charity_id',
      as: 'charity'
    });
    Inventory.belongsTo(models.Item, {
      foreignKey: 'item_id',
      as: 'item'
    });
  };

  return Inventory;
};
